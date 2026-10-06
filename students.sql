SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `student_types`
-- --------------------------------------------------------

CREATE TABLE `student_types` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Data for table `student_types`
-- --------------------------------------------------------

INSERT INTO `student_types` (`id`, `name`) VALUES
(1, 'Physical'),
(2, 'Online'),
(3, 'Hybrid');

-- --------------------------------------------------------
-- Table structure for table `students`
-- --------------------------------------------------------

CREATE TABLE `students` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `other_name` varchar(100) DEFAULT NULL,
    `email` varchar(150) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `guardian_name` varchar(150) NOT NULL,
    `guardian_email` varchar(150) DEFAULT NULL,
    `guardian_phone` varchar(20) NOT NULL,
    `registration_date` date NOT NULL,
    `amount_paid` decimal(12,2) NOT NULL,
    `course_type` varchar(255) DEFAULT NULL,
    `course_description` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT NULL,
    `student_type_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_student_type` (`student_type_id`),
    CONSTRAINT `fk_student_type`
        FOREIGN KEY (`student_type_id`)
        REFERENCES `student_types` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Data for table `students`
-- --------------------------------------------------------

INSERT INTO `students`
(
    `id`,
    `first_name`,
    `last_name`,
    `other_name`,
    `email`,
    `phone`,
    `guardian_name`,
    `guardian_email`,
    `guardian_phone`,
    `registration_date`,
    `amount_paid`,
    `course_type`,
    `course_description`,
    `created_at`,
    `updated_at`,
    `student_type_id`
)
VALUES
(
    1,
    'Jack',
    'Buarar',
    'Nike',
    'jack@gmail.com',
    '07011223344',
    'Mr Johnson',
    'johnson@gmail.com',
    '08123262728',
    '2026-07-04',
    70000.00,
    'Software development',
    'I want to understand how software development works. I want to use this opportunity to intern with you.',
    '2026-07-06 13:58:40',
    NULL,
    1
),
(
    2,
    'Messi',
    'Lionel',
    '',
    'messi@gmail.com',
    '07011223344',
    'Mrs Dorcas',
    'dorcas@gmail.com',
    '07011223344',
    '2026-04-10',
    65000.00,
    'AI integration',
    'The ability to integrate AI in your workflow.',
    '2026-07-06 17:15:13',
    NULL,
    3
),
(
    3,
    'Lamine',
    'Yamal',
    '',
    'yamal@gmail.com',
    '09056532721',
    'Miss Abaab',
    'abaab@gmail.com',
    '09188776655',
    '2025-12-27',
    88500.00,
    'Blockchain developer',
    'Aspiring to be a Blockchain developer.',
    '2026-07-07 14:29:12',
    NULL,
    2
);

COMMIT;