<?php
// models/PatientModel.php

class PatientModel {
    // In a real application, data would come from a database
    public function getAllPatients() {
        return [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'country' => 'USA',
                'status' => 'Stage 1',
                'doctor' => 'Dr. John Doe',
                'condition' => 'Depression',
                'avatar' => 'patient1.png'
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob.smith@example.com',
                'country' => 'Canada',
                'status' => 'Stage 2',
                'doctor' => 'Dr. Jane Smith',
                'condition' => 'Fracture',
                'avatar' => 'patient2.png'
            ],
            [
                'name' => 'Carol Williams',
                'email' => 'carol.williams@example.com',
                'country' => 'UK',
                'status' => 'Stage 3',
                'doctor' => 'Dr. Emily Johnson',
                'condition' => 'Arthritis',
                'avatar' => 'patient3.png'
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@example.com',
                'country' => 'Australia',
                'status' => 'Stage 4',
                'doctor' => 'Dr. Michael Brown',
                'condition' => 'Mumps Stage II',
                'avatar' => 'patient4.png'
            ],
            [
                'name' => 'Eva Davis',
                'email' => 'eva.davis@example.com',
                'country' => 'Germany',
                'status' => 'Stage 1',
                'doctor' => 'Dr. Linda Davis',
                'condition' => 'Depression',
                'avatar' => 'patient5.png'
            ],
        ];
    }
}
?>
