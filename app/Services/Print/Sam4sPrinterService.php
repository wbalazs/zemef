<?php

namespace App\Services\Print;

use Exception;

class Sam4sPrinterService
{
    /**
     * Send QR transaction string to the Sam4s cash machine via serial/USB
     *
     * @param string $qrString
     * @param string $port
     * @param int $baudRate
     * @return bool
     * @throws Exception
     */
    public function sendQrToPrinter(string $qrString, string $port = '/dev/ttyUSB0', int $baudRate = 9600): bool
    {
        if (!extension_loaded('dio')) {
            throw new Exception("PHP dio extension is not loaded. Please install it.");
        }

        try {
            // Open the serial port
            $fd = dio_open($port, O_RDWR | O_NOCTTY | O_NONBLOCK);
            
            if (!$fd) {
                throw new Exception("Cannot open serial port: $port");
            }

            // Configure serial port (for Linux)
            exec("stty -F $port $baudRate cs8 -cstopb -parenb");

            // For Windows, you might need different configuration
            // or use dio_tcsetattr() if available
            
            // Send the QR string
            $bytesWritten = dio_write($fd, $qrString . "\r\n");
            
            if ($bytesWritten === false) {
                dio_close($fd);
                throw new Exception("Failed to write to serial port.");
            }

            // Wait a bit for transmission
            usleep(100000); // 100ms

            // Close the port
            dio_close($fd);

            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to send to Sam4s printer: " . $e->getMessage());
        }
    }
}