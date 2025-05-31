-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 31, 2025 at 08:10 PM
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
-- Database: `project_storage`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"AI-ის მომავალი\", \"en\":\"The Future of AI\"}', '{\"ka\":\"AI ცვლის სამყაროს\", \"en\":\"AI is changing the world\"}', 'ai.jpg', '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"ტექნოლოგია\", \"en\":\"Technology\"}', '2025-05-27 13:44:40', '2025-05-27 13:44:40'),
(2, '{\"ka\":\"ბიზნესი\", \"en\":\"Business\"}', '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `category_program`
--

CREATE TABLE `category_program` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `subject`, `message`, `email`, `created_at`, `updated_at`) VALUES
(1, 'დახმარება', 'მომხმარებლის მხარდაჭერა მჭირდება.', 'help@example.com', '2025-05-27 13:39:37', '2025-05-27 13:39:37'),
(2, 'დახმარება', 'მომხმარებლის მხარდაჭერა მჭირდება.', 'help@example.com', '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `duration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`duration`)),
  `days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`days`)),
  `price` double(8,2) NOT NULL,
  `location` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`location`)),
  `starting_date` date DEFAULT NULL,
  `register_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `image`, `video`, `duration`, `days`, `price`, `location`, `starting_date`, `register_date`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"ლარაველის კურსი\", \"en\":\"Laravel Course\"}', '{\"ka\":\"სწავლე ლარაველი\", \"en\":\"Learn Laravel\"}', 'laravel.jpg', 'intro.mp4', '{\"ka\":\"4 კვირა\", \"en\":\"4 weeks\"}', '{\"ka\":\"ორშაბათი-პარასკევი\", \"en\":\"Mon-Fri\"}', 299.99, '{\"ka\":\"ონლაინი\", \"en\":\"Online\"}', '2025-06-01', '2025-05-15', '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `title`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"საგანმანათლებლო\", \"en\":\"Educational\"}', '2025-05-27 13:37:59', '2025-05-27 13:37:59'),
(2, '{\"ka\":\"საგანმანათლებლო\", \"en\":\"Educational\"}', '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `infos`
--

CREATE TABLE `infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `infos`
--

INSERT INTO `infos` (`id`, `title`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"ჩვენს შესახებ\", \"en\":\"About Us\"}', '{\"ka\":\"ჩვენ ვართ გუნდი...\", \"en\":\"We are a team...\"}', 'https://picsum.photos/800/600', '2025-05-27 13:39:55', '2025-05-27 13:39:55');

-- --------------------------------------------------------

--
-- Table structure for table `main_menus`
--

CREATE TABLE `main_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `link` varchar(255) NOT NULL,
  `target` enum('_self','_blank') NOT NULL DEFAULT '_self',
  `type` enum('default','absolute','dropdown') NOT NULL DEFAULT 'default',
  `sorted` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `visibility` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `main_menus`
--

INSERT INTO `main_menus` (`id`, `title`, `link`, `target`, `type`, `sorted`, `visibility`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"მთავარი\", \"en\":\"Home\"}', 'home.page', '_self', 'default', 1, '1', '2025-05-27 13:44:40', '2025-05-27 13:44:40'),
(20, '{\"ka\":\"ჩვენს შესახებ\", \"en\":\"About Us\"}', 'about.page', '_self', 'default', 2, '1', '2025-05-27 14:04:18', '2025-05-27 14:04:18'),
(21, '{\"ka\":\"კონტაქტი\", \"en\":\"Contact\"}', 'contact.page', '_self', 'default', 7, '1', '2025-05-27 14:04:18', '2025-05-27 14:04:18'),
(22, '{\"ka\":\"პროექტები\", \"en\":\"Projects\"}', 'projects.page', '_self', 'default', 4, '1', '2025-05-27 14:04:18', '2025-05-27 14:04:18'),
(23, '{\"ka\":\"პროგრამები\", \"en\":\"Programs\"}', 'programs.page', '_self', 'default', 5, '1', '2025-05-27 14:04:18', '2025-05-27 14:04:18'),
(24, '{\"ka\":\"სერვისები\", \"en\":\"Services\"}', 'services.page', '_self', 'default', 6, '1', '2025-05-27 14:04:18', '2025-05-27 14:04:18'),
(25, '{\"ka\":\"პუბლიკაციები\", \"en\":\"Publications\"}', 'publications.page', '_self', 'default', 3, '1', '2025-05-27 14:04:18', '2025-05-27 14:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full-name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`id`, `full-name`, `image`, `description`, `created_at`, `updated_at`) VALUES
(1, 'გიორგი ბექაური', 'https://robohash.org/mentor1', 'Senior Laravel Developer', '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `mentor_program`
--

CREATE TABLE `mentor_program` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mentor_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentor_program`
--

INSERT INTO `mentor_program` (`id`, `mentor_id`, `program_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_01_06_113948_create_main_menus_table', 1),
(6, '2024_01_06_113955_create_sub_menus_table', 1),
(7, '2024_01_06_123709_create_posts_table', 1),
(8, '2024_01_06_124110_create_partners_table', 1),
(9, '2024_01_06_124329_create_projects_table', 1),
(10, '2024_01_06_124704_create_courses_table', 1),
(11, '2024_01_06_130350_create_steps_table', 1),
(12, '2024_01_06_130741_create_categories_table', 1),
(13, '2024_01_06_130741_create_genres_table', 1),
(14, '2024_01_06_130833_create_publications_table', 1),
(15, '2024_01_06_131301_create_contacts_table', 1),
(16, '2024_01_07_090143_create_infos_table', 1),
(17, '2024_03_01_141837_create_mentors_table', 1),
(18, '2024_03_01_141954_create_services_table', 1),
(19, '2024_03_01_143654_create_programs_table', 1),
(20, '2024_03_01_150331_create_articles_table', 1),
(21, '2024_09_02_110334_create_category_program_table', 1),
(22, '2025_05_31_104240_create_mentor_program_table', 1),
(23, '2025_05_31_104956_create_syllabuses_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `title`, `image`, `link`, `created_at`, `updated_at`) VALUES
(10, 'AB Security', 'partner-slide-03.jpg', 'https://absecurity.ge/', '2025-05-27 14:17:21', '2025-05-27 14:17:21'),
(11, 'MOH', 'partner-slide-01.jpg', 'https://www.moh.gov.ge/', '2025-05-27 14:17:21', '2025-05-27 14:17:21'),
(12, 'PROFGLDANI', 'partner-slide-08.jpg', 'https://www.profgldani.ge/', '2025-05-27 14:17:21', '2025-05-27 14:17:21'),
(13, 'Victoria Security', 'partner-slide-04.jpg', 'https://www.victoriasecurity.ge/', '2025-05-27 14:17:21', '2025-05-27 14:17:21'),
(14, 'GTU', 'partner-slide-05.jpg', 'https://gtu.ge/', '2025-05-27 14:17:21', '2025-05-27 14:17:21'),
(15, 'EQE', 'partner-slide-07.jpg', 'https://eqe.ge/en', '2025-05-27 14:17:21', '2025-05-27 14:17:21'),
(16, 'Black Sea Group', 'partner-slide-02.jpg', 'https://bsg.com.ge/', '2025-05-27 14:17:21', '2025-05-27 14:17:21');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"პოსტ ტესტი\", \"en\":\"Test Post\"}', '{\"ka\":\"ეს არის აღწერა\", \"en\":\"This is a description\"}', 'post.jpg', '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`days`)),
  `hour` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hour`)),
  `duration` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `visibility` enum('0','1') NOT NULL DEFAULT '1',
  `sortable` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `title`, `description`, `image`, `video`, `price`, `start_date`, `end_date`, `days`, `hour`, `duration`, `address`, `visibility`, `sortable`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"ლიდერობის პროგრამა\", \"en\":\"Leadership Program\"}', '{\"ka\":\"სამხედროების, სამართალდამცავი ორგანოების, დიპლომატებისა და სტუდენტების მომზადების 15-წლიანი გამოცდილებით, CASE წარმოგიდგენთ დაზვერვისა და ეროვნული უსაფრთხოების ინტენსიურ სასწავლო კურსს SSNS25. ზაფხულის სკოლა ყოველწლიურად ტარდება და ათასობით სერტიფიცირებულ პროფესიონალს ითვლის. CASE საუკეთესო ადგილია კარიერული ზრდისა და პროფესიული ქსელური ურთიერთობებისთვის. ეს უნიკალური შესაძლებლობაა უსაფრთხოების სპეციალისტებისა და დაინტერესებული მხარეებისთვის, მიიღონ პირველადი ცოდნა ისეთ თემებზე, რომლებიც არასდროს ყოფილა ასეთი ხელმისაწვდომი, შეიძინონ პროფესიული უნარები და მიიღონ აღიარებული სერტიფიკატი.\", \"en\":\"With 15 years of experience training military, law enforcement, diplomats and students, CASE presents the intensive training course in Intelligence and National Security SSNS25. Summer school is held every year and counts thousands of certified professionals. CASE is the best place for career growth and professional networking. This is a unique opportunity for security professionals and interested parties to gain first-hand insider knowledge on topics that have never been more accessible, gain professional skills and gain recognized certification.\"}', 'img_1.png', 'video.mp4', '500', '2025-07-01', '2025-08-01', '[\"ორშაბათი\", \"სამშაბათი\"]', '{\"start\":\"10:00\", \"end\":\"15:00\"}', '1 თვე', 'თბილისი', '1', 1, '2025-05-27 09:44:40', '2025-05-27 09:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `image`, `created_at`, `updated_at`) VALUES
(2, '{\"ka\":\"ინოვაციური პლატფორმა\", \"en\":\"Innovative Platform\"}', '{\"ka\":\"პლატფორმა ციფრული პროდუქტებისთვის\", \"en\":\"A platform for digital products\"}', '659ea7567ca60-1704896342.png', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(3, '{\"ka\":\"გლობალური სწავლა\", \"en\":\"Global Learning\"}', '{\"ka\":\"გლობალური სასწავლო პროგრამა\", \"en\":\"A global educational initiative\"}', '659eabd360801-1704897491.png', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(4, '{\"ka\":\"ეკო ინიციატივა\", \"en\":\"Eco Initiative\"}', '{\"ka\":\"ეკოლოგიური პროექტი მომავლისთვის\", \"en\":\"An ecological project for the future\"}', 'img_1.png', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(5, '{\"ka\":\"სტარტაპი 2025\", \"en\":\"Startup 2025\"}', '{\"ka\":\"ახალი სტარტაპ იდეა\", \"en\":\"A new startup idea\"}', '659ea7ca96897-1704896458.png', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(6, '{\"ka\":\"კოდის აკადემია\", \"en\":\"Code Academy\"}', '{\"ka\":\"პროექტი პროგრამირების შესასწავლად\", \"en\":\"A project for learning programming\"}', '659ead0227de2-1704897794.png', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(7, '{\"ka\":\"ხელოვნური ინტელექტი\", \"en\":\"Artificial Intelligence\"}', '{\"ka\":\"AI-ზე დაფუძნებული სისტემა\", \"en\":\"An AI-powered system\"}', 'img_2.jpg', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(8, '{\"ka\":\"ქალაქის განახლება\", \"en\":\"City Revamp\"}', '{\"ka\":\"ქალაქის განვითარების გეგმა\", \"en\":\"A plan to revamp urban infrastructure\"}', '659ea8e2edf86-1704896738.png', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(9, '{\"ka\":\"მომავლის სკოლა\", \"en\":\"School of the Future\"}', '{\"ka\":\"ინოვაციური სწავლების მოდელი\", \"en\":\"An innovative learning model\"}', '659fdc2553229-1704975397.png', '2025-05-27 14:11:56', '2025-05-27 14:11:56'),
(10, '{\"ka\":\"ტექნოლოგიური ხაზი\", \"en\":\"Tech Line\"}', '{\"ka\":\"ტექნოლოგიური სერვისების ქსელი\", \"en\":\"A network of tech services\"}', 'project1.jpg', '2025-05-27 14:11:56', '2025-05-27 14:11:56');

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) NOT NULL,
  `created` date DEFAULT NULL,
  `file` varchar(255) NOT NULL,
  `genre_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `publications`
--

INSERT INTO `publications` (`id`, `title`, `description`, `image`, `created`, `file`, `genre_id`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"პუბლიკაცია 1\", \"en\":\"Publication 1\"}', '{\"ka\":\"აღწერა\", \"en\":\"Description\"}', 'img_1.jpg', '2025-04-20', 'doc.pdf', 1, '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `image` varchar(255) NOT NULL,
  `visibility` enum('0','1') NOT NULL DEFAULT '1',
  `sortable` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `image`, `visibility`, `sortable`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"სერვისი\", \"en\":\"Service\"}', '{\"ka\":\"სერვისის აღწერა\", \"en\":\"Service description\"}', 'service.jpg', '1', 1, '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `steps`
--

CREATE TABLE `steps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `steps`
--

INSERT INTO `steps` (`id`, `title`, `description`, `course_id`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"ნაბიჯი 1\", \"en\":\"Step 1\"}', '{\"ka\":\"ნაბიჯის აღწერა\", \"en\":\"Step description\"}', 1, '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `sub_menus`
--

CREATE TABLE `sub_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `link` varchar(255) NOT NULL,
  `target` enum('_self','_blank') NOT NULL DEFAULT '_self',
  `type` enum('default','absolute','dropdown') NOT NULL DEFAULT 'default',
  `sorted` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `visibility` enum('0','1') NOT NULL DEFAULT '1',
  `main_menu_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_menus`
--

INSERT INTO `sub_menus` (`id`, `title`, `link`, `target`, `type`, `sorted`, `visibility`, `main_menu_id`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"კურსები\", \"en\":\"Courses\"}', '/courses', '_self', 'default', 1, '1', 1, '2025-05-27 13:44:40', '2025-05-27 13:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `syllabuses`
--

CREATE TABLE `syllabuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `pdf` varchar(255) NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabuses`
--

INSERT INTO `syllabuses` (`id`, `title`, `pdf`, `program_id`, `created_at`, `updated_at`) VALUES
(1, '{\"ka\":\"სილაბუსის სათაური\", \"en\":\"Syllabus title\"}', 'sample-document.pdf', 1, '2025-05-31 11:35:35', '2025-05-31 11:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_program`
--
ALTER TABLE `category_program`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_program_category_id_foreign` (`category_id`),
  ADD KEY `category_program_program_id_foreign` (`program_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `infos`
--
ALTER TABLE `infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `main_menus`
--
ALTER TABLE `main_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mentor_program`
--
ALTER TABLE `mentor_program`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentor_program_mentor_id_foreign` (`mentor_id`),
  ADD KEY `mentor_program_program_id_foreign` (`program_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publications_genre_id_foreign` (`genre_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `steps`
--
ALTER TABLE `steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `steps_course_id_foreign` (`course_id`);

--
-- Indexes for table `sub_menus`
--
ALTER TABLE `sub_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_menus_main_menu_id_foreign` (`main_menu_id`);

--
-- Indexes for table `syllabuses`
--
ALTER TABLE `syllabuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `syllabuses_program_id_foreign` (`program_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category_program`
--
ALTER TABLE `category_program`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `infos`
--
ALTER TABLE `infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `main_menus`
--
ALTER TABLE `main_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mentor_program`
--
ALTER TABLE `mentor_program`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sub_menus`
--
ALTER TABLE `sub_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `syllabuses`
--
ALTER TABLE `syllabuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category_program`
--
ALTER TABLE `category_program`
  ADD CONSTRAINT `category_program_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_program_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_program`
--
ALTER TABLE `mentor_program`
  ADD CONSTRAINT `mentor_program_mentor_id_foreign` FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentor_program_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`);

--
-- Constraints for table `steps`
--
ALTER TABLE `steps`
  ADD CONSTRAINT `steps_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `sub_menus`
--
ALTER TABLE `sub_menus`
  ADD CONSTRAINT `sub_menus_main_menu_id_foreign` FOREIGN KEY (`main_menu_id`) REFERENCES `main_menus` (`id`);

--
-- Constraints for table `syllabuses`
--
ALTER TABLE `syllabuses`
  ADD CONSTRAINT `syllabuses_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
