<?php
// models/DoctorModel.php

class DoctorModel {
    // In a real application, data would come from a database
    public function getAllDoctors() {
        return [
            [
                'name' => 'Dr. John Doe',
                'email' => 'johndoe@example.com',
                'speciality' => 'Cardiologist',
                'contact_no' => '+1 555-123-4567',
                'avatar' => 'doctor1.png'
            ],
            [
                'name' => 'Dr. Jane Smith',
                'email' => 'janesmith@example.com',
                'speciality' => 'Neurologist',
                'contact_no' => '+1 555-234-5678',
                'avatar' => 'doctor2.png'
            ],
            [
                'name' => 'Dr. Emily Johnson',
                'email' => 'emilyjohnson@example.com',
                'speciality' => 'Pediatrician',
                'contact_no' => '+1 555-345-6789',
                'avatar' => 'doctor3.png'
            ],
            [
                'name' => 'Dr. Michael Brown',
                'email' => 'michaelbrown@example.com',
                'speciality' => 'Dermatologist',
                'contact_no' => '+1 555-456-7890',
                'avatar' => 'doctor4.png'
            ],
            [
                'name' => 'Dr. Linda Davis',
                'email' => 'lindadavis@example.com',
                'speciality' => 'Oncologist',
                'contact_no' => '+1 555-567-8901',
                'avatar' => 'doctor5.png'
            ],
        ];
    }
}
?>
