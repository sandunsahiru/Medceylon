<?php
// models/AppointmentModel.php

class AppointmentModel {
    // In a real application, data would come from a database
    public function getSummaryData() {
        return [
            'new_appointments' => 20,
            'completed_appointments' => 104,
            'patients' => 24
        ];
    }

    public function getNewAppointments() {
        return [
            [
                'name' => 'Leslie Alexander',
                'email' => 'leslie.alexander@example.com',
                'date' => '10/10/2020',
                'visit_time' => '09:15-09:45 AM',
                'country' => 'Canada',
                'condition' => 'Mumps Stage II',
                'avatar' => 'patient1.png'
            ],
            // Add other patient data...
            [
                'name' => 'Ronald Richards',
                'email' => 'ronald.richards@example.com',
                'date' => '10/12/2020',
                'visit_time' => '12:00-12:45 PM',
                'country' => 'Australia',
                'condition' => 'Depression',
                'avatar' => 'patient2.png'
            ],
            [
                'name' => 'Jane Cooper',
                'email' => 'jane.cooper@example.com',
                'date' => '10/13/2020',
                'visit_time' => '01:15-01:45 PM',
                'country' => 'Germany',
                'condition' => 'Arthritis',
                'avatar' => 'patient3.png'
            ],
            [
                'name' => 'Robert Fox',
                'email' => 'robert.fox@example.com',
                'date' => '10/14/2020',
                'visit_time' => '02:00-02:45 PM',
                'country' => 'France',
                'condition' => 'Fracture',
                'avatar' => 'patient4.png'
            ],
            [
                'name' => 'Jenny Wilson',
                'email' => 'jenny.wilson@example.com',
                'date' => '10/15/2020',
                'visit_time' => '12:00-12:45 PM',
                'country' => 'Canada',
                'condition' => 'Depression',
                'avatar' => 'patient5.png'
            ]
        ];
    }
}
?>
