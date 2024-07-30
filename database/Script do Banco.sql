-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 18/07/2024 às 10:10
-- Versão do servidor: 10.11.8-MariaDB-cll-lve
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u512569378_contador_jw`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cnpj_cpf` varchar(45) DEFAULT NULL,
  `corporate_name` varchar(191) NOT NULL,
  `fantasy_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `public_place` varchar(191) DEFAULT NULL,
  `home_number` varchar(35) DEFAULT NULL,
  `complement` varchar(191) DEFAULT NULL,
  `district` varchar(191) DEFAULT NULL,
  `zip_code` varchar(191) DEFAULT NULL,
  `county` varchar(191) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `companies`
--

INSERT INTO `companies` (`id`, `cnpj_cpf`, `corporate_name`, `fantasy_name`, `email`, `public_place`, `home_number`, `complement`, `district`, `zip_code`, `county`, `uf`, `phone_number`, `created_at`, `updated_at`) VALUES
(1, '00000000000000', 'EMPRESA MODELO', 'EMPRESA MODELO', 'empresa@gmail.com', 'AV. BRASIL', '100', NULL, 'JD. BELO HORIZONTE', '88100300', 'APARECIDA DE GOIANIA', 'GO', '19998764512', NULL, '2024-07-18 07:09:13');

-- --------------------------------------------------------

--
-- Estrutura para tabela `disable_documents`
--

CREATE TABLE `disable_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `environment_type` varchar(1) DEFAULT NULL,
  `service` varchar(45) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `year` varchar(45) DEFAULT NULL,
  `cnpj` varchar(45) DEFAULT NULL,
  `model` int(10) UNSIGNED DEFAULT NULL,
  `series` bigint(20) UNSIGNED DEFAULT NULL,
  `number_start` bigint(20) UNSIGNED DEFAULT NULL,
  `number_end` bigint(20) UNSIGNED DEFAULT NULL,
  `event_dh` timestamp NULL DEFAULT NULL,
  `event_status` varchar(45) DEFAULT NULL,
  `protocol_number` varchar(45) DEFAULT NULL,
  `justification` varchar(191) DEFAULT NULL,
  `size` double DEFAULT NULL,
  `path_xml` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cnpj_emit` varchar(45) DEFAULT NULL,
  `cnpj_cpf` varchar(45) NOT NULL,
  `ie` varchar(45) NOT NULL,
  `model` int(10) UNSIGNED NOT NULL,
  `series` bigint(20) UNSIGNED NOT NULL,
  `number` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(80) NOT NULL,
  `month_year` varchar(6) NOT NULL,
  `issue_dh` date NOT NULL,
  `path_xml` longtext NOT NULL,
  `protocol` varchar(80) NOT NULL,
  `environment_type` varchar(1) NOT NULL,
  `status_xml` varchar(10) NOT NULL,
  `size` double DEFAULT NULL,
  `vNF` double NOT NULL,
  `entrada` varchar(2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `event_documents`
--

CREATE TABLE `event_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `environment_type` varchar(1) DEFAULT NULL,
  `cnpj` varchar(45) DEFAULT NULL,
  `model` int(10) UNSIGNED DEFAULT NULL,
  `nfe_key` varchar(50) DEFAULT NULL,
  `event_dh` timestamp NULL DEFAULT NULL,
  `event_type` varchar(45) DEFAULT NULL,
  `event_number` bigint(20) UNSIGNED DEFAULT NULL,
  `event_desc` varchar(191) DEFAULT NULL,
  `event_status` varchar(45) DEFAULT NULL,
  `protocol_number` varchar(45) DEFAULT NULL,
  `justification` varchar(191) DEFAULT NULL,
  `correction` varchar(191) DEFAULT NULL,
  `size` double DEFAULT NULL,
  `path_xml` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('ph.silvasantos@hotmail.com', '$2y$10$/upD8QJNnUDP699cdKSP3eyS2Z3jBXRXyYGlFJz.mGvx6EcA0WWDS', '2024-07-09 01:17:21');

-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_admin` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `is_admin`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@admin.com', NULL, 'S', '$2y$10$rgZKFDrziO/ad83MnGtlh.wDKlxRFt0S/TN7HQO50R96iTIsjsZf6', NULL, NULL, '2024-07-18 07:07:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_company`
--

CREATE TABLE `user_company` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `user_company`
--

INSERT INTO `user_company` (`user_id`, `company_id`) VALUES
(1, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `companies_corporate_name_unique` (`corporate_name`) USING BTREE,
  ADD UNIQUE KEY `companies_cnpj_cpf_unique` (`cnpj_cpf`) USING BTREE;

--
-- Índices de tabela `disable_documents`
--
ALTER TABLE `disable_documents`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `event_documents`
--
ALTER TABLE `event_documents`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE;

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`) USING BTREE;

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`) USING BTREE,
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`) USING BTREE;

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_email_unique` (`email`) USING BTREE;

--
-- Índices de tabela `user_company`
--
ALTER TABLE `user_company`
  ADD KEY `user_company_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `user_company_company_id_foreign` (`company_id`) USING BTREE;

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `disable_documents`
--
ALTER TABLE `disable_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16933;

--
-- AUTO_INCREMENT de tabela `event_documents`
--
ALTER TABLE `event_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `user_company`
--
ALTER TABLE `user_company`
  ADD CONSTRAINT `user_company_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `user_company_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
