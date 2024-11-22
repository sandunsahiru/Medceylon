-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2024 at 08:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medceylon`
--

-- --------------------------------------------------------

--
-- Table structure for table `accommodationassistance`
--

CREATE TABLE `accommodationassistance` (
  `accommodation_request_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `accommodation_type` varchar(50) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('Pending','Booked','Canceled') DEFAULT 'Pending',
  `accommodation_provider_id` int(11) DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodationassistance`
--

INSERT INTO `accommodationassistance` (`accommodation_request_id`, `patient_id`, `check_in_date`, `check_out_date`, `accommodation_type`, `special_requests`, `status`, `accommodation_provider_id`, `last_updated`) VALUES
(1, 1, '2024-09-19', '2024-09-21', 'Standard Room', 'Wheelchair access needed', 'Booked', 1, '2024-09-16 12:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `accommodationproviders`
--

CREATE TABLE `accommodationproviders` (
  `provider_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `address_line1` varchar(100) DEFAULT NULL,
  `address_line2` varchar(100) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `services_offered` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodationproviders`
--

INSERT INTO `accommodationproviders` (`provider_id`, `name`, `contact_info`, `address_line1`, `address_line2`, `city_id`, `services_offered`) VALUES
(1, 'Comfort Stay', '+94112223344', '12 Lake Rd', '', 1, 'Short-term accommodation near hospitals'),
(2, 'Healing Homes', '+94812223344', '34 Hill Rd', '', 2, 'Patient-friendly lodging');

-- --------------------------------------------------------

--
-- Table structure for table `accreditationdetails`
--

CREATE TABLE `accreditationdetails` (
  `accreditation_id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `accreditation_body` varchar(100) DEFAULT NULL,
  `accreditation_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `accreditation_documents` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accreditationdetails`
--

INSERT INTO `accreditationdetails` (`accreditation_id`, `hospital_id`, `accreditation_body`, `accreditation_date`, `expiration_date`, `accreditation_documents`) VALUES
(1, 1, 'Joint Commission International', '2022-01-01', '2025-01-01', 'assets/documents/jci_accreditation.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `appointmentdocuments`
--

CREATE TABLE `appointmentdocuments` (
  `document_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointmentdocuments`
--

INSERT INTO `appointmentdocuments` (`document_id`, `appointment_id`, `document_type`, `file_path`, `upload_date`) VALUES
(1, 1, 'Prescription', 'assets/documents/prescription_appointment1.pdf', '2024-09-16 12:20:13');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `appointment_status` enum('Scheduled','Completed','Canceled','Rescheduled') DEFAULT 'Scheduled',
  `booking_date` datetime DEFAULT current_timestamp(),
  `consultation_type` enum('Online','In-Person') DEFAULT 'Online',
  `reason_for_visit` text DEFAULT NULL,
  `rescheduled_from` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `appointment_status`, `booking_date`, `consultation_type`, `reason_for_visit`, `rescheduled_from`, `notes`) VALUES
(1, 1, 1, '2024-09-20', '10:00:00', 'Scheduled', '2024-09-16 12:11:14', 'In-Person', 'Regular check-up', NULL, NULL),
(2, 5, 1, '2024-09-21', '11:00:00', 'Scheduled', '2024-09-16 12:11:14', 'Online', 'Chest pain consultation', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `country_code` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`city_id`, `city_name`, `country_code`) VALUES
(1, 'Colombo', 'LK'),
(2, 'Kandy', 'LK'),
(3, 'Galle', 'LK'),
(4, 'New York', 'US'),
(5, 'London', 'GB'),
(6, 'Sydney', 'AU');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `participant1_id` int(11) DEFAULT NULL,
  `participant2_id` int(11) DEFAULT NULL,
  `last_message_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `participant1_id`, `participant2_id`, `last_message_time`) VALUES
(1, 1, 2, '2024-09-16 12:17:55'),
(2, 1, 4, '2024-09-16 12:17:55');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `country_code` char(2) NOT NULL,
  `country_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`country_code`, `country_name`) VALUES
('AU', 'Australia'),
('GB', 'United Kingdom'),
('IN', 'India'),
('LK', 'Sri Lanka'),
('US', 'United States');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `years_of_experience` int(11) DEFAULT NULL,
  `profile_description` text DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `hospital_id`, `qualifications`, `years_of_experience`, `profile_description`, `license_number`, `is_verified`, `is_active`) VALUES
(1, 2, 1, 'MBBS, MD (Cardiology)', 15, 'Experienced cardiologist specializing in heart diseases.', 'DOC123456', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctorspecializations`
--

CREATE TABLE `doctorspecializations` (
  `doctor_id` int(11) NOT NULL,
  `specialization_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctorspecializations`
--

INSERT INTO `doctorspecializations` (`doctor_id`, `specialization_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `upload_date` datetime DEFAULT current_timestamp(),
  `associated_with_type` varchar(50) DEFAULT NULL,
  `associated_with_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`document_id`, `user_id`, `document_type`, `file_path`, `upload_date`, `associated_with_type`, `associated_with_id`) VALUES
(1, 1, 'Medical Report', 'assets/documents/medical_report_john_doe.pdf', '2024-09-16 12:17:35', 'Appointment', 1),
(2, 2, 'License Certificate', 'assets/documents/license_dr_smith.pdf', '2024-09-16 12:17:35', 'Doctor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `faq_id` int(11) NOT NULL,
  `question` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`faq_id`, `question`, `answer`, `category`) VALUES
(1, 'How to book an appointment?', 'You can book an appointment by...', 'Appointments'),
(2, 'What payment methods are accepted?', 'We accept credit cards and...', 'Payments');

-- --------------------------------------------------------

--
-- Table structure for table `healthrecords`
--

CREATE TABLE `healthrecords` (
  `record_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment_plan` text DEFAULT NULL,
  `prescriptions` text DEFAULT NULL,
  `test_results` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `healthrecords`
--

INSERT INTO `healthrecords` (`record_id`, `patient_id`, `doctor_id`, `appointment_id`, `diagnosis`, `treatment_plan`, `prescriptions`, `test_results`, `date_created`, `date_modified`) VALUES
(1, 1, 1, 1, 'Healthy', 'Maintain regular exercise', NULL, NULL, '2024-09-16 12:11:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospitaladmins`
--

CREATE TABLE `hospitaladmins` (
  `hospital_admin_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitaladmins`
--

INSERT INTO `hospitaladmins` (`hospital_admin_id`, `user_id`, `hospital_id`) VALUES
(1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `hospital_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address_line1` varchar(100) DEFAULT NULL,
  `address_line2` varchar(100) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`hospital_id`, `name`, `address_line1`, `address_line2`, `city_id`, `country_code`, `contact_number`, `email`, `website`, `description`, `latitude`, `longitude`, `is_active`) VALUES
(1, 'Colombo General Hospital', '95 Kynsey Rd', '', 1, 'LK', '+94112345678', 'info@colombogh.lk', 'www.colombogh.lk', 'Leading government hospital in Colombo.', 6.915700, 79.861200, 1),
(2, 'Kandy Teaching Hospital', 'Anniewatta Rd', '', 2, 'LK', '+94812345678', 'info@kandyhospital.lk', 'www.kandyhospital.lk', 'Main hospital in Kandy.', 7.290600, 80.633700, 1);

-- --------------------------------------------------------

--
-- Table structure for table `hospitalservices`
--

CREATE TABLE `hospitalservices` (
  `service_id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `service_name` varchar(100) DEFAULT NULL,
  `service_description` text DEFAULT NULL,
  `service_cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitalservices`
--

INSERT INTO `hospitalservices` (`service_id`, `hospital_id`, `service_name`, `service_description`, `service_cost`) VALUES
(1, 1, 'Cardiac Surgery', 'Advanced heart surgeries', 5000.00),
(2, 1, 'General Consultation', 'Outpatient services', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `hotel_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address_line1` varchar(100) NOT NULL,
  `address_line2` varchar(100) DEFAULT NULL,
  `city_id` int(11) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`hotel_id`, `name`, `address_line1`, `address_line2`, `city_id`, `contact_number`, `email`, `description`, `image1`, `image2`, `image3`, `image4`) VALUES
(1, 'Sea Breeze Hotel', '50 Beach Rd', '', 1, '+94112223344', 'seabreeze@example.com', 'Hotel with ocean views.', 'assets/images/hotels/seabreeze1.jpg', 'assets/images/hotels/seabreeze2.jpg', 'assets/images/hotels/seabreeze3.jpg', 'assets/images/hotels/seabreeze4.jpg'),
(2, 'Mountain View Inn', '88 Peak Rd', '', 2, '+94812223344', 'mountainview@example.com', 'Cozy inn with mountain views.', 'assets/images/hotels/mountainview1.jpg', 'assets/images/hotels/mountainview2.jpg', 'assets/images/hotels/mountainview3.jpg', 'assets/images/hotels/mountainview4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_rooms`
--

CREATE TABLE `hotel_rooms` (
  `room_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `availability` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_rooms`
--

INSERT INTO `hotel_rooms` (`room_id`, `hotel_id`, `room_type`, `price_per_night`, `availability`, `description`) VALUES
(1, 1, 'Standard Room', 80.00, 5, 'Comfortable room with basic amenities.'),
(2, 1, 'Deluxe Room', 120.00, 3, 'Spacious room with sea view.'),
(3, 2, 'Single Room', 60.00, 4, 'Cozy room for solo travelers.'),
(4, 2, 'Family Suite', 150.00, 2, 'Large suite suitable for families.');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message_text` text DEFAULT NULL,
  `sent_time` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `sender_id`, `message_text`, `sent_time`, `is_read`) VALUES
(1, 1, 1, 'Hello Dr. Smith, I have a question about my medication.', '2024-09-16 12:18:06', 0),
(2, 1, 2, 'Hello John, sure, please let me know.', '2024-09-16 12:18:06', 0),
(3, 2, 1, 'I need help with my booking.', '2024-09-16 12:18:06', 0),
(4, 2, 4, 'How can I assist you?', '2024-09-16 12:18:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `notification_text` text DEFAULT NULL,
  `notification_type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `notification_text`, `notification_type`, `is_read`, `date_created`) VALUES
(1, 1, 'Your appointment with Dr. Smith is confirmed for 2024-09-20.', 'Appointment', 0, '2024-09-16 12:18:21'),
(2, 2, 'You have a new message from John Doe.', 'Message', 0, '2024-09-16 12:18:21'),
(3, 1, 'Your room booking at Sea Breeze Hotel is confirmed.', 'Booking', 0, '2024-09-16 12:18:21');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `date_published` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `title`, `content`, `resource_type`, `category`, `date_published`) VALUES
(1, 'Healthy Living Tips', 'Content about healthy living...', 'Article', 'Health', '2024-09-16 12:17:02'),
(2, 'Appointment Guide', 'How to schedule appointments...', 'PDF', 'User Guide', '2024-09-16 12:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `patient_id`, `doctor_id`, `hospital_id`, `rating`, `review_text`, `review_date`) VALUES
(1, 1, 1, 1, 5, 'Excellent care provided by Dr. Smith.', '2024-09-16 12:16:16'),
(2, 5, NULL, 2, 4, 'Friendly staff and clean facilities.', '2024-09-16 12:16:16');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(20) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Patient', 'User seeking medical services'),
(2, 'Doctor', 'Medical professional providing services'),
(3, 'HospitalAdmin', 'Administrator managing hospital data'),
(4, 'SystemAdmin', 'Administrator managing the system'),
(5, 'SupportAgent', 'User providing support services');

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `booking_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `guests` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Canceled') DEFAULT 'Pending',
  `booking_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`booking_id`, `patient_id`, `room_id`, `check_in_date`, `check_out_date`, `guests`, `special_requests`, `status`, `booking_date`) VALUES
(1, 1, 2, '2024-09-19', '2024-09-21', 1, 'High floor room', 'Confirmed', '2024-09-16 12:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

CREATE TABLE `specializations` (
  `specialization_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`specialization_id`, `name`, `description`) VALUES
(1, 'Cardiology', 'Heart and cardiovascular system'),
(2, 'Neurology', 'Brain and nervous system'),
(3, 'Orthopedics', 'Bones and musculoskeletal system');

-- --------------------------------------------------------

--
-- Table structure for table `supporttickets`
--

CREATE TABLE `supporttickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `issue_category` varchar(100) DEFAULT NULL,
  `issue_description` text DEFAULT NULL,
  `status` enum('Open','In Progress','Closed') DEFAULT 'Open',
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `support_agent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supporttickets`
--

INSERT INTO `supporttickets` (`ticket_id`, `user_id`, `issue_category`, `issue_description`, `status`, `date_created`, `date_updated`, `support_agent_id`) VALUES
(1, 1, 'Booking Issue', 'Unable to confirm hotel booking.', 'Open', '2024-09-16 12:16:33', '2024-09-16 12:16:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticketmessages`
--

CREATE TABLE `ticketmessages` (
  `message_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message_text` text DEFAULT NULL,
  `sent_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticketmessages`
--

INSERT INTO `ticketmessages` (`message_id`, `ticket_id`, `sender_id`, `message_text`, `sent_time`) VALUES
(1, 1, 1, 'I tried booking a room but received an error.', '2024-09-16 12:16:49'),
(2, 1, 4, 'We are looking into this issue.', '2024-09-16 12:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `transportationassistance`
--

CREATE TABLE `transportationassistance` (
  `transport_request_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `transport_type` varchar(50) DEFAULT NULL,
  `pickup_location` varchar(100) DEFAULT NULL,
  `dropoff_location` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `status` enum('Pending','Booked','Completed','Canceled') DEFAULT 'Pending',
  `transport_provider_id` int(11) DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transportationassistance`
--

INSERT INTO `transportationassistance` (`transport_request_id`, `patient_id`, `transport_type`, `pickup_location`, `dropoff_location`, `date`, `time`, `status`, `transport_provider_id`, `last_updated`) VALUES
(1, 1, 'Taxi', 'Bandaranaike Airport', 'Sea Breeze Hotel', '2024-09-19', '08:00:00', 'Booked', 1, '2024-09-16 12:14:18');

-- --------------------------------------------------------

--
-- Table structure for table `transportproviders`
--

CREATE TABLE `transportproviders` (
  `provider_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `services_offered` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transportproviders`
--

INSERT INTO `transportproviders` (`provider_id`, `name`, `contact_info`, `services_offered`) VALUES
(1, 'City Taxi', '+94119998888', 'Airport transfers, hospital pickups'),
(2, 'Quick Ambulance', '+94118887777', 'Emergency and non-emergency transport');

-- --------------------------------------------------------

--
-- Table structure for table `userroles`
--

CREATE TABLE `userroles` (
  `username` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userroles`
--

INSERT INTO `userroles` (`username`, `role_id`) VALUES
('jane_doe', 1),
('john_doe', 1),
('dr_smith', 2),
('admin_user', 4),
('support_agent', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address_line1` varchar(100) DEFAULT NULL,
  `address_line2` varchar(100) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `nationality` char(2) DEFAULT NULL,
  `passport_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `first_name`, `last_name`, `date_of_birth`, `gender`, `phone_number`, `address_line1`, `address_line2`, `city_id`, `nationality`, `passport_number`, `profile_picture`, `role_id`, `registration_date`, `last_login`, `is_active`) VALUES
(1, 'john_doe', '482c811da5d5b4bc6d497ffa98491e38', 'john.doe@example.com', 'John', 'Doe', '1985-06-15', 'Male', '+94112345678', '123 Main St', '', 1, 'US', 'A1234567', 'assets/images/profile_john_doe.jpg', 1, '2024-09-16 12:08:25', NULL, 1),
(2, 'dr_smith', '482c811da5d5b4bc6d497ffa98491e38', 'dr.smith@example.com', 'Emily', 'Smith', '1975-04-20', 'Female', '+94115556789', '456 High St', '', 1, 'GB', 'B7654321', 'assets/images/profile_dr_smith.jpg', 2, '2024-09-16 12:08:25', NULL, 1),
(3, 'admin_user', '25e4ee4e9229397b6b17776bfceaf8e7', 'admin@example.com', 'Admin', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '2024-09-16 12:08:25', NULL, 1),
(4, 'support_agent', 'a8736dd296ccfcd52d3c94e09614e528', 'support@example.com', 'Support', 'Agent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, '2024-09-16 12:08:25', NULL, 1),
(5, 'jane_doe', '482c811da5d5b4bc6d497ffa98491e38', 'jane.doe@example.com', 'Jane', 'Doe', '1990-08-25', 'Female', '+94113334455', '789 Park Ave', '', 2, 'LK', 'C9876543', 'assets/images/profile_jane_doe.jpg', 1, '2024-09-16 12:08:25', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `visaassistance`
--

CREATE TABLE `visaassistance` (
  `visa_request_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `visa_type` varchar(50) DEFAULT NULL,
  `application_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Submitted','Approved','Rejected') DEFAULT 'Pending',
  `assistance_agent_id` int(11) DEFAULT NULL,
  `visa_details` text DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visaassistance`
--

INSERT INTO `visaassistance` (`visa_request_id`, `patient_id`, `visa_type`, `application_date`, `status`, `assistance_agent_id`, `visa_details`, `last_updated`) VALUES
(1, 1, 'Medical Visa', '2024-09-16 12:14:35', 'Approved', 4, 'Visa approved for treatment purposes.', '2024-09-16 12:14:35');

-- --------------------------------------------------------

--
-- Table structure for table `visadocuments`
--

CREATE TABLE `visadocuments` (
  `document_id` int(11) NOT NULL,
  `visa_request_id` int(11) DEFAULT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visadocuments`
--

INSERT INTO `visadocuments` (`document_id`, `visa_request_id`, `document_type`, `file_path`, `upload_date`) VALUES
(1, 1, 'Passport Copy', 'assets/documents/passport_john_doe.pdf', '2024-09-16 12:15:27'),
(2, 1, 'Medical Report', 'assets/documents/medical_report_john_doe.pdf', '2024-09-16 12:15:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodationassistance`
--
ALTER TABLE `accommodationassistance`
  ADD PRIMARY KEY (`accommodation_request_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `accommodation_provider_id` (`accommodation_provider_id`);

--
-- Indexes for table `accommodationproviders`
--
ALTER TABLE `accommodationproviders`
  ADD PRIMARY KEY (`provider_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `accreditationdetails`
--
ALTER TABLE `accreditationdetails`
  ADD PRIMARY KEY (`accreditation_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `appointmentdocuments`
--
ALTER TABLE `appointmentdocuments`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `rescheduled_from` (`rescheduled_from`),
  ADD KEY `idx_appointments_patient_id` (`patient_id`),
  ADD KEY `idx_appointments_doctor_id` (`doctor_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `country_code` (`country_code`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `participant1_id` (`participant1_id`),
  ADD KEY `participant2_id` (`participant2_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_code`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_doctors_hospital_id` (`hospital_id`),
  ADD KEY `idx_doctors_user_id` (`user_id`);

--
-- Indexes for table `doctorspecializations`
--
ALTER TABLE `doctorspecializations`
  ADD PRIMARY KEY (`doctor_id`,`specialization_id`),
  ADD KEY `specialization_id` (`specialization_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`faq_id`);

--
-- Indexes for table `healthrecords`
--
ALTER TABLE `healthrecords`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `hospitaladmins`
--
ALTER TABLE `hospitaladmins`
  ADD PRIMARY KEY (`hospital_admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`hospital_id`),
  ADD KEY `country_code` (`country_code`),
  ADD KEY `idx_hospitals_city_id` (`city_id`);

--
-- Indexes for table `hospitalservices`
--
ALTER TABLE `hospitalservices`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`hotel_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`specialization_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `supporttickets`
--
ALTER TABLE `supporttickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `support_agent_id` (`support_agent_id`);

--
-- Indexes for table `ticketmessages`
--
ALTER TABLE `ticketmessages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `transportationassistance`
--
ALTER TABLE `transportationassistance`
  ADD PRIMARY KEY (`transport_request_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `transport_provider_id` (`transport_provider_id`);

--
-- Indexes for table `transportproviders`
--
ALTER TABLE `transportproviders`
  ADD PRIMARY KEY (`provider_id`);

--
-- Indexes for table `userroles`
--
ALTER TABLE `userroles`
  ADD PRIMARY KEY (`username`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `idx_userroles_username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD KEY `nationality` (`nationality`),
  ADD KEY `idx_users_role_id` (`role_id`),
  ADD KEY `idx_users_city_id` (`city_id`);

--
-- Indexes for table `visaassistance`
--
ALTER TABLE `visaassistance`
  ADD PRIMARY KEY (`visa_request_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `assistance_agent_id` (`assistance_agent_id`);

--
-- Indexes for table `visadocuments`
--
ALTER TABLE `visadocuments`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `visa_request_id` (`visa_request_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accommodationassistance`
--
ALTER TABLE `accommodationassistance`
  MODIFY `accommodation_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `accommodationproviders`
--
ALTER TABLE `accommodationproviders`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `accreditationdetails`
--
ALTER TABLE `accreditationdetails`
  MODIFY `accreditation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointmentdocuments`
--
ALTER TABLE `appointmentdocuments`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `healthrecords`
--
ALTER TABLE `healthrecords`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospitaladmins`
--
ALTER TABLE `hospitaladmins`
  MODIFY `hospital_admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hospitalservices`
--
ALTER TABLE `hospitalservices`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `specialization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supporttickets`
--
ALTER TABLE `supporttickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ticketmessages`
--
ALTER TABLE `ticketmessages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transportationassistance`
--
ALTER TABLE `transportationassistance`
  MODIFY `transport_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transportproviders`
--
ALTER TABLE `transportproviders`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visaassistance`
--
ALTER TABLE `visaassistance`
  MODIFY `visa_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visadocuments`
--
ALTER TABLE `visadocuments`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accommodationassistance`
--
ALTER TABLE `accommodationassistance`
  ADD CONSTRAINT `accommodationassistance_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `accommodationassistance_ibfk_2` FOREIGN KEY (`accommodation_provider_id`) REFERENCES `accommodationproviders` (`provider_id`);

--
-- Constraints for table `accommodationproviders`
--
ALTER TABLE `accommodationproviders`
  ADD CONSTRAINT `accommodationproviders_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`);

--
-- Constraints for table `accreditationdetails`
--
ALTER TABLE `accreditationdetails`
  ADD CONSTRAINT `accreditationdetails_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

--
-- Constraints for table `appointmentdocuments`
--
ALTER TABLE `appointmentdocuments`
  ADD CONSTRAINT `appointmentdocuments_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`);

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`rescheduled_from`) REFERENCES `appointments` (`appointment_id`);

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_code`) REFERENCES `countries` (`country_code`);

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`participant1_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`participant2_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

--
-- Constraints for table `doctorspecializations`
--
ALTER TABLE `doctorspecializations`
  ADD CONSTRAINT `doctorspecializations_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `doctorspecializations_ibfk_2` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`specialization_id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `healthrecords`
--
ALTER TABLE `healthrecords`
  ADD CONSTRAINT `healthrecords_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `healthrecords_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `healthrecords_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`);

--
-- Constraints for table `hospitaladmins`
--
ALTER TABLE `hospitaladmins`
  ADD CONSTRAINT `hospitaladmins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `hospitaladmins_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

--
-- Constraints for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD CONSTRAINT `hospitals_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`),
  ADD CONSTRAINT `hospitals_ibfk_2` FOREIGN KEY (`country_code`) REFERENCES `countries` (`country_code`);

--
-- Constraints for table `hospitalservices`
--
ALTER TABLE `hospitalservices`
  ADD CONSTRAINT `hospitalservices_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`);

--
-- Constraints for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD CONSTRAINT `hotel_rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

--
-- Constraints for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD CONSTRAINT `room_bookings_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `room_bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `hotel_rooms` (`room_id`);

--
-- Constraints for table `supporttickets`
--
ALTER TABLE `supporttickets`
  ADD CONSTRAINT `supporttickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `supporttickets_ibfk_2` FOREIGN KEY (`support_agent_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ticketmessages`
--
ALTER TABLE `ticketmessages`
  ADD CONSTRAINT `ticketmessages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `supporttickets` (`ticket_id`),
  ADD CONSTRAINT `ticketmessages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transportationassistance`
--
ALTER TABLE `transportationassistance`
  ADD CONSTRAINT `transportationassistance_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `transportationassistance_ibfk_2` FOREIGN KEY (`transport_provider_id`) REFERENCES `transportproviders` (`provider_id`);

--
-- Constraints for table `userroles`
--
ALTER TABLE `userroles`
  ADD CONSTRAINT `userroles_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userroles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`nationality`) REFERENCES `countries` (`country_code`),
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `visaassistance`
--
ALTER TABLE `visaassistance`
  ADD CONSTRAINT `visaassistance_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `visaassistance_ibfk_2` FOREIGN KEY (`assistance_agent_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `visadocuments`
--
ALTER TABLE `visadocuments`
  ADD CONSTRAINT `visadocuments_ibfk_1` FOREIGN KEY (`visa_request_id`) REFERENCES `visaassistance` (`visa_request_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
