<?php
// models/AccommodationModel.php

class AccommodationModel
{
    private $db;

    public function __construct()
    {
        // Initialize database connection
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=medceylon', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set charset to UTF-8
            $this->db->exec("SET NAMES 'utf8mb4';");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Fetch hospital city based on patient ID
    public function getHospitalCityByPatient($patientId)
    {
        $stmt = $this->db->prepare("
            SELECT c.city_id, c.city_name
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.doctor_id
            JOIN hospitals h ON d.hospital_id = h.hospital_id
            JOIN cities c ON h.city_id = c.city_id
            WHERE a.patient_id = :patient_id
            ORDER BY a.appointment_date DESC
            LIMIT 1
        ");
        $stmt->execute(['patient_id' => $patientId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    // Fetch hotels in the city
    public function getHotelsByCity($cityId)
    {
        $stmt = $this->db->prepare("SELECT * FROM hotels WHERE city_id = :city_id");
        $stmt->execute(['city_id' => $cityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Fetch all rooms for a hotel
    public function getHotelRooms($hotelId)
    {
        $stmt = $this->db->prepare("SELECT * FROM hotel_rooms WHERE hotel_id = :hotel_id");
        $stmt->execute(['hotel_id' => $hotelId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Fetch available rooms for a hotel within the given dates and guest count
    public function getAvailableRooms($hotelId, $checkInDate, $checkOutDate, $guests)
    {
        $stmt = $this->db->prepare("
            SELECT hr.*
            FROM hotel_rooms hr
            WHERE hr.hotel_id = :hotel_id
              AND hr.max_guests >= :guests
              AND hr.room_id NOT IN (
                SELECT rb.room_id
                FROM room_bookings rb
                WHERE rb.hotel_id = :hotel_id
                  AND rb.status != 'Canceled'
                  AND (
                    (:check_in_date BETWEEN rb.check_in_date AND rb.check_out_date)
                    OR
                    (:check_out_date BETWEEN rb.check_in_date AND rb.check_out_date)
                    OR
                    (rb.check_in_date BETWEEN :check_in_date AND :check_out_date)
                  )
              )
        ");
        $stmt->execute([
            'hotel_id' => $hotelId,
            'guests' => $guests,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Save room booking
    public function saveRoomBooking($patientId, $bookingData)
    {
        $stmt = $this->db->prepare("
            INSERT INTO room_bookings
            (patient_id, room_id, check_in_date, check_out_date, guests, special_requests, status)
            VALUES
            (:patient_id, :room_id, :check_in_date, :check_out_date, :guests, :special_requests, 'Pending')
        ");

        $stmt->execute([
            'patient_id' => $patientId,
            'room_id' => $bookingData['room_id'],
            'check_in_date' => $bookingData['check_in_date'],
            'check_out_date' => $bookingData['check_out_date'],
            'guests' => $bookingData['guests'],
            'special_requests' => $bookingData['special_requests']
        ]);

        return $this->db->lastInsertId();
    }

    // Fetch hotel details by ID
    public function getHotelById($hotelId)
    {
        $stmt = $this->db->prepare("SELECT * FROM hotels WHERE hotel_id = :hotel_id");
        $stmt->execute(['hotel_id' => $hotelId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
