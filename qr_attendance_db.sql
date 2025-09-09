-- Set up the database
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = '+08:00';

-- Create `tbl_attendance`
DROP TABLE IF EXISTS tbl_attendance;
CREATE TABLE tbl_attendance (
  tbl_attendance_id INT(11) NOT NULL AUTO_INCREMENT,
  tbl_student_id INT(11) NOT NULL,
  time_in TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  time_out TIMESTAMP NULL DEFAULT NULL, -- Allows NULL time_out if not set
  PRIMARY KEY (tbl_attendance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data into `tbl_attendance`
INSERT INTO `tbl_attendance` (`tbl_attendance_id`, `tbl_student_id`, `time_in`) VALUES
(2, 1, '2024-03-13 00:45:37');

-- Create `tbl_student` with separated `course` and `year_level`
DROP TABLE IF EXISTS tbl_student;
CREATE TABLE tbl_student (
  `tbl_student_id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_no` VARCHAR(255) NOT NULL,
  `student_name` VARCHAR(255) NOT NULL,
  `course` VARCHAR(255) NOT NULL,
  `year_level` ENUM('1st Year', '2nd Year', '3rd Year', '4th Year') NOT NULL,
  `generated_code` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`tbl_student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data into `tbl_student`
INSERT INTO `tbl_student` (`tbl_student_id`, `student_no`, `student_name`, `course`, `year_level`, `generated_code`) VALUES
(1, 'Samantha', 'BSIS', '4th Year', 'KIYkAk6ZRV');

-- Create `users` table
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create `tbl_attendance_statistics`
DROP TABLE IF EXISTS tbl_attendance_statistics;
CREATE TABLE tbl_attendance_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course VARCHAR(100) NOT NULL,
    total_count INT NOT NULL DEFAULT 0
);
COMMIT;
