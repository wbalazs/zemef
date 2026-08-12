<?php

namespace App\Services\Inventory;

use App\Models\InternalRelationTransit\InternalRelationTransit;
use App\Models\Assignment\DataTransferObjects\AssignmentData;
use App\Models\Route\Route;
use DateTime;
use function trans;
use DB;

class ImportLisService
{

    /** @var AssignmentData The provided parameters. */
    protected $data;
    protected $dates = [];
    protected $dates_of_route = [];
    protected $dates_of_header;
    protected $date_start;
    protected $date_end;

    /**
     *  Create an instance of the AssignmentData service.
     *
     *  @return void
     */
    public function __construct(AssignmentData $data)
    {
        $this->data = $data;
        $this->getStartAndEndDate(($this->data->week - 1), $this->data->year, $this->data->number_of_days);
    }
    
    /**
     *  Create users,profiles records and return the associated user.
     *
     *  @return self
     */
    public function handle()
    {
        $relations = [];
        $json = json_decode($this->data->user->options, true);

        if ($json && array_key_exists('relations', $json) && sizeof($json['relations'])) {
            $relations = InternalRelationTransit::whereIn('id', $json['relations'])->get();
        } else {
            $relations = InternalRelationTransit::all();
        }

        foreach ($relations as $relation) {
            $routes = Route::whereBetween('date', [$this->date_start,$this->date_end])
                ->where('relation_id', $relation->id);

            if ($this->data->route_number) {
                $routes->where('route_number','like', '%'.$this->data->route_number.'%');
            }

            if (!$this->data->all) {
                $routes->where('driver_id', $this->data->user?->id);
            }

            $this->setDatesOfRoute($routes->get(), $relation);
        }
        foreach ($relations as $relation) {
            $this->setDriverAlerts();
            $this->setVehicleAlerts();
        }
        
        $this->data->dates_of_header = $this->dates_of_header;
        $this->data->generate_array = $this->dates_of_route;
        
        return $this->data;
    }

     /**
     *  Create and return an bool.
     *
     *  @param  AssignmentData  $data

     *  @return AssignmentData
     */
    public static function From(AssignmentData $data): AssignmentData
    {
        return (new self($data))->handle();
    }

    public function setDatesOfRoute($routes, $relation) {
       /* if (!$routes->count()) {
            return null;
        }
        */
        $temp = $this->dates;

        foreach ($routes as $route) {
            $temp[date_format($route->date, 'Y-m-d')] = json_encode(
                [
                    'route_id' => $route->id,
                    'relation_name' => $route->driver?->name,
                    'driver_name' => $route->driver?->name,
                    'driver_id' => $route->driver?->id,
                    'main_vehicle_id' => $route->main_vehicle_id,
                    'license_plate' => $route->mainVehicle?->license_plate,
                    'status' => $route->status,
                    'alert' => null,
                ]
            );
        }

        $this->dates_of_route[$relation->id] = array_merge([$relation->name => $relation->id], $temp);
    }
    public function setDriverAlerts() {
        
        $alerts = DB::select('SELECT GROUP_CONCAT(driver_id) as cnt, date FROM raorg.routes where date between "'.$this->date_start.'" and "'.$this->date_end.'" group by date having cnt > 0');
        
        foreach ($alerts as $key => $value) {
            $findDuplicate = array_diff_assoc( 
                explode(',', $value->cnt), 
                array_unique(explode(',', $value->cnt)) 
            ); 
            if (sizeof($findDuplicate)) {
               
                foreach ($this->dates_of_route as $key => $routes) {
                    if ($routes[$value->date] == null) continue;
                    
                    $temp = json_decode($routes[$value->date], true);

                    if (in_array($temp['driver_id'], $findDuplicate)) {
                        $temp['alert'] = 1;
                        $this->dates_of_route[$key][$value->date] = json_encode($temp);   
                    }
                }
            }
        }
    }
    public function setVehicleAlerts() {
        
        $alerts = DB::select('SELECT GROUP_CONCAT(main_vehicle_id) as cnt, date FROM raorg.routes where date between "'.$this->date_start.'" and "'.$this->date_end.'" group by date having cnt > 0');
        
        foreach ($alerts as $key => $value) {
            $findDuplicate = array_diff_assoc( 
                explode(',', $value->cnt), 
                array_unique(explode(',', $value->cnt)) 
            ); 
            if (sizeof($findDuplicate)) {
               
                foreach ($this->dates_of_route as $key => $routes) {
                    if ($routes[$value->date] == null) continue;
                    
                    $temp = json_decode($routes[$value->date], true);

                    if (in_array($temp['main_vehicle_id'], $findDuplicate)) {
                        $temp['alert'] = 1;
                        $this->dates_of_route[$key][$value->date] = json_encode($temp);   
                    }
                }
            }
        }
    }

    public function getStartAndEndDate($week, $year, $number_of_days) {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $this->date_start = $dto->format('Y-m-d');
        $dto->modify("+{$number_of_days} days");
        $this->date_end = $dto->format('Y-m-d');

        $dto->setISODate($year, $week);

        for ($i = 0; $i < $number_of_days ;$i++) {
            $this->dates_of_header[$dto->format('Y-m-d')] = trans("location.date.date_days.{$dto->format('D')}");
            $this->dates[$dto->format('Y-m-d')] = null;
            $dto->modify("+1 days");
        }
    }
        
}
