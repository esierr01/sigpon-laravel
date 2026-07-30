-- Sequence and defined type
CREATE SEQUENCE IF NOT EXISTS log_change_id_seq;
CREATE SEQUENCE IF NOT EXISTS generals_id_seq;
CREATE SEQUENCE IF NOT EXISTS categories_id_seq;
CREATE SEQUENCE IF NOT EXISTS equipment_id_seq;
CREATE SEQUENCE IF NOT EXISTS brand_models_id_seq;
CREATE SEQUENCE IF NOT EXISTS units_id_seq;
CREATE SEQUENCE IF NOT EXISTS inventory_id_seq;
CREATE SEQUENCE IF NOT EXISTS stores_id_seq;
CREATE SEQUENCE IF NOT EXISTS movements_id_seq;
CREATE SEQUENCE IF NOT EXISTS suppliers_id_seq;
CREATE SEQUENCE IF NOT EXISTS movement_types_id_seq;
CREATE SEQUENCE IF NOT EXISTS roles_id_seq;
CREATE SEQUENCE IF NOT EXISTS migrations_id_seq;
CREATE SEQUENCE IF NOT EXISTS users_id_seq;
CREATE SEQUENCE IF NOT EXISTS jobs_id_seq;
CREATE SEQUENCE IF NOT EXISTS failed_jobs_id_seq;
CREATE SEQUENCE IF NOT EXISTS log_access_id_seq;

-- Table Definition
CREATE TABLE "public"."log_change" (
    "id" int8 NOT NULL DEFAULT nextval('log_change_id_seq'::regclass),
    "user_id" int8,
    "table" varchar(30) NOT NULL,
    "obs" varchar(255) NOT NULL,
    "ip" varchar(45) NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT "log_change_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE SET NULL,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."generals" (
    "id" int8 NOT NULL DEFAULT nextval('generals_id_seq'::regclass),
    "rif" varchar(20) NOT NULL,
    "department" varchar(255) NOT NULL,
    "title_report_1" varchar(255),
    "subtitle_report_1" varchar(255),
    "title_report_2" varchar(255),
    "subtitle_report_2" varchar(255),
    "title_report_3" varchar(255),
    "subtitle_report_3" varchar(255),
    "title_report_4" varchar(255),
    "subtitle_report_4" varchar(255),
    "footer" varchar(255),
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."categories" (
    "id" int8 NOT NULL DEFAULT nextval('categories_id_seq'::regclass),
    "name" varchar(100) NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."equipment" (
    "id" int8 NOT NULL DEFAULT nextval('equipment_id_seq'::regclass),
    "sku" varchar(50) NOT NULL,
    "category_id" int8 NOT NULL,
    "name" varchar(255) NOT NULL,
    "brand_model_id" int8 NOT NULL,
    "unit_id" int8 NOT NULL,
    "umbral" int4 NOT NULL DEFAULT 0,
    "active" bool NOT NULL DEFAULT true,
    "img_url_one" varchar(255),
    "img_url_two" varchar(255),
    "user_id" int8 NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT "equipment_category_id_foreign" FOREIGN KEY ("category_id") REFERENCES "public"."categories"("id") ON DELETE RESTRICT,
    CONSTRAINT "equipment_brand_model_id_foreign" FOREIGN KEY ("brand_model_id") REFERENCES "public"."brand_models"("id") ON DELETE RESTRICT,
    CONSTRAINT "equipment_unit_id_foreign" FOREIGN KEY ("unit_id") REFERENCES "public"."units"("id") ON DELETE RESTRICT,
    CONSTRAINT "equipment_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE RESTRICT,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."brand_models" (
    "id" int8 NOT NULL DEFAULT nextval('brand_models_id_seq'::regclass),
    "brand" varchar(100) NOT NULL,
    "model" varchar(100) NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."units" (
    "id" int8 NOT NULL DEFAULT nextval('units_id_seq'::regclass),
    "name" varchar(20) NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."inventory" (
    "id" int8 NOT NULL DEFAULT nextval('inventory_id_seq'::regclass),
    "equipment_id" int8 NOT NULL,
    "store_id" int8 NOT NULL,
    "stock" int4 NOT NULL DEFAULT 0,
    "last_change" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "user_id" int8 NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT "inventory_equipment_id_foreign" FOREIGN KEY ("equipment_id") REFERENCES "public"."equipment"("id") ON DELETE CASCADE,
    CONSTRAINT "inventory_store_id_foreign" FOREIGN KEY ("store_id") REFERENCES "public"."stores"("id") ON DELETE CASCADE,
    CONSTRAINT "inventory_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE RESTRICT,
    PRIMARY KEY ("id")
);


-- Indices
CREATE UNIQUE INDEX inventory_equipment_id_store_id_unique ON public.inventory USING btree (equipment_id, store_id);

-- Table Definition
CREATE TABLE "public"."stores" (
    "id" int8 NOT NULL DEFAULT nextval('stores_id_seq'::regclass),
    "name" varchar(255) NOT NULL,
    "address" varchar(255) NOT NULL,
    "phone" varchar(255) NOT NULL,
    "contact" varchar(255) NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."movements" (
    "id" int8 NOT NULL DEFAULT nextval('movements_id_seq'::regclass),
    "movement_type" int4 NOT NULL,
    "equipment_id" int8 NOT NULL,
    "supplier_id" int8,
    "origin_id" int8,
    "destination_id" int8,
    "amount" int4 NOT NULL,
    "obs" varchar(255),
    "user_id" int8 NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT "movements_equipment_id_foreign" FOREIGN KEY ("equipment_id") REFERENCES "public"."equipment"("id") ON DELETE RESTRICT,
    CONSTRAINT "movements_supplier_id_foreign" FOREIGN KEY ("supplier_id") REFERENCES "public"."suppliers"("id") ON DELETE RESTRICT,
    CONSTRAINT "movements_origin_id_foreign" FOREIGN KEY ("origin_id") REFERENCES "public"."stores"("id") ON DELETE RESTRICT,
    CONSTRAINT "movements_destination_id_foreign" FOREIGN KEY ("destination_id") REFERENCES "public"."stores"("id") ON DELETE RESTRICT,
    CONSTRAINT "movements_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE RESTRICT,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."suppliers" (
    "id" int8 NOT NULL DEFAULT nextval('suppliers_id_seq'::regclass),
    "name" varchar(255) NOT NULL,
    "address" varchar(255) NOT NULL,
    "phone" varchar(255) NOT NULL,
    "contact" varchar(255) NOT NULL,
    "rif" varchar(20) NOT NULL,
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."movement_types" (
    "id" int8 NOT NULL DEFAULT nextval('movement_types_id_seq'::regclass),
    "name" varchar(50) NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    PRIMARY KEY ("id")
);


-- Indices
CREATE UNIQUE INDEX movement_types_name_unique ON public.movement_types USING btree (name);

-- Table Definition
CREATE TABLE "public"."roles" (
    "id" int8 NOT NULL DEFAULT nextval('roles_id_seq'::regclass),
    "name" varchar(50) NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    PRIMARY KEY ("id")
);


-- Indices
CREATE UNIQUE INDEX roles_name_unique ON public.roles USING btree (name);

-- Table Definition
CREATE TABLE "public"."migrations" (
    "id" int4 NOT NULL DEFAULT nextval('migrations_id_seq'::regclass),
    "migration" varchar(255) NOT NULL,
    "batch" int4 NOT NULL,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."users" (
    "id" int8 NOT NULL DEFAULT nextval('users_id_seq'::regclass),
    "name" varchar(255) NOT NULL,
    "email" varchar(255) NOT NULL,
    "email_verified_at" timestamp(0),
    "password" varchar(255) NOT NULL,
    "remember_token" varchar(100),
    "role" int4 NOT NULL DEFAULT 3,
    "active" bool NOT NULL DEFAULT true,
    "last_login" timestamp(0),
    "create_for" int8,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    CONSTRAINT "users_create_for_foreign" FOREIGN KEY ("create_for") REFERENCES "public"."users"("id") ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY ("id")
);


-- Indices
CREATE UNIQUE INDEX users_email_unique ON public.users USING btree (email);

-- Table Definition
CREATE TABLE "public"."password_reset_tokens" (
    "email" varchar(255) NOT NULL,
    "token" varchar(255) NOT NULL,
    "created_at" timestamp(0),
    PRIMARY KEY ("email")
);

-- Table Definition
CREATE TABLE "public"."sessions" (
    "id" varchar(255) NOT NULL,
    "user_id" int8,
    "ip_address" varchar(45),
    "user_agent" text,
    "payload" text NOT NULL,
    "last_activity" int4 NOT NULL,
    PRIMARY KEY ("id")
);


-- Indices
CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);
CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);

-- Table Definition
CREATE TABLE "public"."cache" (
    "key" varchar(255) NOT NULL,
    "value" text NOT NULL,
    "expiration" int4 NOT NULL,
    PRIMARY KEY ("key")
);


-- Indices
CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);

-- Table Definition
CREATE TABLE "public"."cache_locks" (
    "key" varchar(255) NOT NULL,
    "owner" varchar(255) NOT NULL,
    "expiration" int4 NOT NULL,
    PRIMARY KEY ("key")
);


-- Indices
CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);

-- Table Definition
CREATE TABLE "public"."jobs" (
    "id" int8 NOT NULL DEFAULT nextval('jobs_id_seq'::regclass),
    "queue" varchar(255) NOT NULL,
    "payload" text NOT NULL,
    "attempts" int2 NOT NULL,
    "reserved_at" int4,
    "available_at" int4 NOT NULL,
    "created_at" int4 NOT NULL,
    PRIMARY KEY ("id")
);


-- Indices
CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);

-- Table Definition
CREATE TABLE "public"."job_batches" (
    "id" varchar(255) NOT NULL,
    "name" varchar(255) NOT NULL,
    "total_jobs" int4 NOT NULL,
    "pending_jobs" int4 NOT NULL,
    "failed_jobs" int4 NOT NULL,
    "failed_job_ids" text NOT NULL,
    "options" text,
    "cancelled_at" int4,
    "created_at" int4 NOT NULL,
    "finished_at" int4,
    PRIMARY KEY ("id")
);

-- Table Definition
CREATE TABLE "public"."failed_jobs" (
    "id" int8 NOT NULL DEFAULT nextval('failed_jobs_id_seq'::regclass),
    "uuid" varchar(255) NOT NULL,
    "connection" text NOT NULL,
    "queue" text NOT NULL,
    "payload" text NOT NULL,
    "exception" text NOT NULL,
    "failed_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);


-- Indices
CREATE UNIQUE INDEX failed_jobs_uuid_unique ON public.failed_jobs USING btree (uuid);

-- Table Definition
CREATE TABLE "public"."log_access" (
    "id" int8 NOT NULL DEFAULT nextval('log_access_id_seq'::regclass),
    "mail" varchar(100) NOT NULL,
    "result" bool NOT NULL,
    "obs" varchar(255),
    "created_at" timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

INSERT INTO "public"."log_change" ("id", "user_id", "table", "obs", "ip", "created_at") VALUES
(1, 1, 'users', 'Modificación de usuario coco@gmail.com | name: antes ''Coco Sierra'' - ahora ''Coco Sierra Contreras''', '127.0.0.1', '2026-06-11 23:15:48'),
(2, 1, 'users', 'Inhabilitación de usuario: lucas@gmail.com', '127.0.0.1', '2026-06-11 23:16:17'),
(3, 1, 'users', 'Carga de nuevo usuario: prueba_user@gmail.com', '127.0.0.1', '2026-06-11 23:16:59'),
(4, 1, 'users', 'Habilitación de usuario: relleno@gmail.com', '127.0.0.1', '2026-06-11 23:17:21'),
(5, 1, 'users', 'Inhabilitación de usuario: visitante@gmail.com', '127.0.0.1', '2026-06-11 23:28:46'),
(6, 1, 'users', 'Inhabilitación de usuario: prueba@gmail.com', '127.0.0.1', '2026-06-11 23:28:49'),
(7, 1, 'users', 'Inhabilitación de usuario: editor@gmail.com', '127.0.0.1', '2026-06-11 23:28:53'),
(8, 1, 'users', 'Inhabilitación de usuario: relleno@gmail.com', '127.0.0.1', '2026-06-11 23:28:56'),
(9, 1, 'users', 'Modificación de usuario lucas@gmail.com | name: antes ''lucas adams'' - ahora ''lucas adams (el de la película de los locos adams)''', '127.0.0.1', '2026-06-11 23:29:40'),
(10, 1, 'users', 'Modificación de usuario prueba_user@gmail.com | role: antes ''Editor'' - ahora ''Admin'', Contraseña actualizada', '127.0.0.1', '2026-06-11 23:31:42'),
(11, NULL, 'users', 'Modificación de usuario lucas@gmail.com | name: antes ''lucas adams (el de la película de los locos adams)'' - ahora ''lucas adams (el de la película de los locos adams) , otro loco mas''', '127.0.0.1', '2026-06-11 23:32:11'),
(12, NULL, 'users', 'Modificación de usuario visitante@gmail.com | name: antes ''Luiz Muñoz'' - ahora ''Luiz Muñoz (er rolo de loco)''', '127.0.0.1', '2026-06-11 23:32:22'),
(13, 1, 'categories', 'Carga de nuevo registro: {"name":"ODF 48 posiciones"}', '127.0.0.1', '2026-06-12 20:10:48'),
(14, 1, 'categories', 'Modificación de registro ID 1 | name: antes ''ODF 48 posiciones'' - ahora ''ODF 48 posiciones hor''', '127.0.0.1', '2026-06-12 20:11:29'),
(15, 1, 'units', 'Carga de nuevo registro: {"name":"Unidad"}', '127.0.0.1', '2026-06-12 20:12:15'),
(16, 1, 'units', 'Carga de nuevo registro: {"name":"Metro"}', '127.0.0.1', '2026-06-12 20:12:23'),
(17, 1, 'units', 'Modificación de registro ID 2 | name: antes ''Metro'' - ahora ''Metros''', '127.0.0.1', '2026-06-12 20:12:41'),
(18, 1, 'brand_models', 'Carga de nuevo registro: {"brand":"Huawei","model":"HTR-258P"}', '127.0.0.1', '2026-06-12 20:13:53'),
(19, 1, 'brand_models', 'Carga de nuevo registro: {"brand":"ZTE","model":"ADR2600"}', '127.0.0.1', '2026-06-12 20:14:03'),
(20, 1, 'brand_models', 'Modificación de registro ID 1 | model: antes ''HTR-258P'' - ahora ''HTR-258PI''', '127.0.0.1', '2026-06-12 20:14:09'),
(21, 1, 'suppliers', 'Carga de nuevo registro: {"name":"ZTE","address":"Principal de los Ruices, Municipio Sucre, Estado Miranda, edificio Union, PB","phone":"0212-6898920","contact":"Luis Mejias","rif":"J-154544543-2"}', '127.0.0.1', '2026-06-12 20:15:41'),
(22, 1, 'stores', 'Carga de nuevo registro: {"name":"La Yaguara","address":"Av. Principal de La Yaguara, antes de Makro, Centro Logistico La Yaguara","phone":"0212-4512356","contact":"Pedro Garc\u00eda"}', '127.0.0.1', '2026-06-12 20:16:59'),
(23, 1, 'stores', 'Modificación de registro ID 1 | address: antes ''Av. Principal de La Yaguara, antes de Makro, Centro Logistico La Yaguara'' - ahora ''Av. Principal de La Yaguara, antes de Makro, Bajada mueblerias, Centro Logistico La Yaguara''', '127.0.0.1', '2026-06-12 20:17:35'),
(24, 1, 'suppliers', 'Modificación de registro ID 1 | contact: antes ''Luis Mejias'' - ahora ''Luis Mejias Linares''', '127.0.0.1', '2026-06-12 20:17:49'),
(25, 1, 'categories', 'Carga de nuevo registro: {"name":"Cable FO 12 hilos interno"}', '127.0.0.1', '2026-06-12 20:23:03'),
(26, 1, 'categories', 'Carga de nuevo registro: {"name":"Cable FO 24 hilos interno"}', '127.0.0.1', '2026-06-12 20:23:21'),
(27, 1, 'categories', 'Carga de nuevo registro: {"name":"Cable 48 hilos interno"}', '127.0.0.1', '2026-06-12 20:23:34'),
(28, 1, 'categories', 'Carga de nuevo registro: {"name":"Cable 48 hilos externo"}', '127.0.0.1', '2026-06-12 20:23:44'),
(29, 1, 'categories', 'Carga de nuevo registro: {"name":"Cable 48 hilos autosoportado"}', '127.0.0.1', '2026-06-12 20:23:57'),
(30, 1, 'units', 'Carga de nuevo registro: {"name":"Metros"}', '127.0.0.1', '2026-06-13 10:13:51'),
(31, 1, 'units', 'Eliminación de registro ID 3', '127.0.0.1', '2026-06-13 10:13:56'),
(32, 1, 'users', 'Carga de nuevo usuario: pepe@gmail.com', '127.0.0.1', '2026-06-13 10:25:15'),
(33, 1, 'units', 'Carga de nuevo registro: {"name":"Metros"}', '127.0.0.1', '2026-06-13 10:28:59'),
(34, 1, 'units', 'Eliminación de registro ID 4', '127.0.0.1', '2026-06-13 10:29:09'),
(35, 1, 'units', 'Eliminación de registro ID 1', '127.0.0.1', '2026-06-13 10:29:28'),
(36, 1, 'units', 'Carga de nuevo registro: {"name":"Quintal"}', '127.0.0.1', '2026-06-13 10:30:53'),
(37, 1, 'units', 'Eliminación de registro ID 5', '127.0.0.1', '2026-06-13 10:31:15'),
(38, 1, 'generals', 'Modificación de datos generales: rif, department, title_report_1, subtitle_report_1, title_report_2, subtitle_report_2, title_report_3, subtitle_report_3, title_report_4, subtitle_report_4, footer', '127.0.0.1', '2026-06-13 18:42:21'),
(39, 1, 'users', 'Cambio de contraseña propio', '127.0.0.1', '2026-06-13 18:56:32'),
(40, 1, 'users', 'Cambio de contraseña propio', '127.0.0.1', '2026-06-13 18:59:47'),
(41, 1, 'units', 'Carga de nuevo registro: {"name":"Rollo"}', '127.0.0.1', '2026-06-13 20:11:27'),
(42, 1, 'categories', 'Carga de nuevo registro: {"name":"Cables"}', '127.0.0.1', '2026-06-15 18:34:25'),
(43, 1, 'categories', 'Carga de nuevo registro: {"name":"Switches"}', '127.0.0.1', '2026-06-15 18:34:41'),
(44, 1, 'categories', 'Carga de nuevo registro: {"name":"ODF"}', '127.0.0.1', '2026-06-15 18:34:47'),
(45, 1, 'categories', 'Carga de nuevo registro: {"name":"Patchcord"}', '127.0.0.1', '2026-06-15 18:35:02'),
(46, 1, 'units', 'Carga de nuevo registro: {"name":"Unidad"}', '127.0.0.1', '2026-06-15 18:35:16'),
(47, 1, 'units', 'Carga de nuevo registro: {"name":"Metros"}', '127.0.0.1', '2026-06-15 18:35:25'),
(48, 1, 'units', 'Carga de nuevo registro: {"name":"Caja"}', '127.0.0.1', '2026-06-15 18:35:35'),
(49, 1, 'brand_models', 'Carga de nuevo registro: {"brand":"ZTE","model":"Roccan18"}', '127.0.0.1', '2026-06-15 18:35:47'),
(50, 1, 'brand_models', 'Carga de nuevo registro: {"brand":"ZTE","model":"AMR12"}', '127.0.0.1', '2026-06-15 18:35:54'),
(51, 1, 'brand_models', 'Carga de nuevo registro: {"brand":"Huawei","model":"P28"}', '127.0.0.1', '2026-06-15 18:36:05'),
(52, 1, 'brand_models', 'Carga de nuevo registro: {"brand":"Huwei","model":"6100"}', '127.0.0.1', '2026-06-15 18:36:13'),
(53, 1, 'suppliers', 'Carga de nuevo registro: {"name":"Huawei","address":"Los Ruices, cerca VTV","phone":"04125553232","contact":"Luis Alfonzo","rif":"J-154544543-2"}', '127.0.0.1', '2026-06-15 18:36:49'),
(54, 1, 'stores', 'Carga de nuevo registro: {"name":"Almac\u00e9n Transitorio Equipos II","address":"CNT, Av Libertador","phone":"0212-5001041","contact":"Rodolfo Pacheco"}', '127.0.0.1', '2026-06-15 18:37:21'),
(55, 1, 'stores', 'Carga de nuevo registro: {"name":"Ca\u00f1o Amarillo","address":"Maracay","phone":"0243-5263636","contact":"Krisoft Perez"}', '127.0.0.1', '2026-06-15 18:37:42'),
(56, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 1', '127.0.0.1', '2026-06-15 18:43:25'),
(57, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 1', '127.0.0.1', '2026-06-15 18:53:11'),
(58, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:09:26'),
(59, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:11:39'),
(60, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:12:15'),
(61, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:12:55'),
(62, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:14:02'),
(63, 1, 'movements', 'Nuevo movimiento tipo 2 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:15:08'),
(64, 1, 'movements', 'Nuevo movimiento tipo 3 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:16:01'),
(65, 1, 'movements', 'Nuevo movimiento tipo 4 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:17:12'),
(66, 1, 'movements', 'Nuevo movimiento tipo 4 - Equipo ID: 2', '127.0.0.1', '2026-06-15 20:18:04'),
(67, 1, 'categories', 'Carga de nuevo registro: {"name":"Conector"}', '127.0.0.1', '2026-06-16 17:11:34'),
(68, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 3', '127.0.0.1', '2026-06-16 17:13:37'),
(69, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 3', '127.0.0.1', '2026-06-16 17:20:51'),
(70, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 4', '127.0.0.1', '2026-06-16 21:03:18'),
(71, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 4', '127.0.0.1', '2026-06-16 21:16:40'),
(72, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 5', '127.0.0.1', '2026-06-16 21:28:01'),
(73, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 6', '127.0.0.1', '2026-06-16 21:30:54'),
(74, 1, 'categories', 'Eliminación de registro ID 10', '127.0.0.1', '2026-06-20 16:15:42'),
(75, 1, 'units', 'Carga de nuevo registro: {"name":"Paquetes"}', '127.0.0.1', '2026-06-20 16:16:04'),
(76, 1, 'units', 'Eliminación de registro ID 10', '127.0.0.1', '2026-06-20 16:16:09'),
(77, 1, 'brand_models', 'Eliminación de registro ID 6', '127.0.0.1', '2026-06-20 16:16:48'),
(78, 1, 'brand_models', 'Modificación de registro ID 5 | model: antes ''P28'' - ahora ''P2800''', '127.0.0.1', '2026-06-20 16:17:29'),
(79, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 7', '127.0.0.1', '2026-06-20 19:08:09'),
(80, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 7', '127.0.0.1', '2026-06-20 19:16:33'),
(81, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 7', '127.0.0.1', '2026-06-20 19:31:19'),
(82, 1, 'movements', 'Nuevo movimiento tipo 2 - Equipo ID: 6', '127.0.0.1', '2026-06-20 21:02:04'),
(83, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 6', '127.0.0.1', '2026-06-20 21:02:37'),
(84, 1, 'movements', 'Nuevo movimiento tipo 2 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:08:51'),
(85, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:09:22'),
(86, 1, 'movements', 'Nuevo movimiento tipo 2 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:09:40'),
(87, 1, 'movements', 'Nuevo movimiento tipo 3 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:10:44'),
(88, 1, 'movements', 'Nuevo movimiento tipo 3 - Equipo ID: 6', '127.0.0.1', '2026-06-20 21:17:26'),
(89, 1, 'movements', 'Nuevo movimiento tipo 3 - Equipo ID: 6', '127.0.0.1', '2026-06-20 21:17:55'),
(90, 1, 'movements', 'Nuevo movimiento tipo 3 - Equipo ID: 6', '127.0.0.1', '2026-06-20 21:19:28'),
(91, 1, 'movements', 'Nuevo movimiento tipo 4 - Equipo ID: 6', '127.0.0.1', '2026-06-20 21:26:52'),
(92, 1, 'movements', 'Nuevo movimiento tipo 1 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:27:56'),
(93, 1, 'movements', 'Nuevo movimiento tipo 2 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:28:37'),
(94, 1, 'movements', 'Nuevo movimiento tipo 3 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:29:05'),
(95, 1, 'movements', 'Nuevo movimiento tipo 4 - Equipo ID: 7', '127.0.0.1', '2026-06-20 21:29:33'),
(96, 1, 'movements', 'Compra de inventario (+400) a equipo "Televisor Smart"', '127.0.0.1', '2026-06-20 21:43:51'),
(97, 1, 'movements', 'Salida de inventario (-100) a equipo "Televisor Smart"', '127.0.0.1', '2026-06-20 21:44:16'),
(98, 1, 'movements', 'Traslado de 50 unidades a equipo "Televisor Smart"', '127.0.0.1', '2026-06-20 21:44:43'),
(99, 1, 'movements', 'Ajuste de inventario (-50) a equipo "Televisor Smart"', '127.0.0.1', '2026-06-20 21:45:03'),
(100, 1, 'movements', 'Compra de inventario (+200) de equipo "Pendrive de 64GB" del Proveedor Huawei hacia el Almacen Almacén Transitorio Equipos II', '127.0.0.1', '2026-06-20 21:52:30'),
(101, 1, 'movements', 'Salida de inventario (-34) de equipo "Pendrive de 64GB" del Almacen Almacén Transitorio Equipos II', '127.0.0.1', '2026-06-20 21:52:56'),
(102, 1, 'movements', 'Traslado de 56 unidades de equipo "Pendrive de 64GB" del Almacen Almacén Transitorio Equipos II hacia el Almacen Caño Amarillo', '127.0.0.1', '2026-06-20 21:53:21'),
(103, 1, 'movements', 'Ajuste de inventario (-10) de equipo "Pendrive de 64GB" en Almacen Almacén Transitorio Equipos II', '127.0.0.1', '2026-06-20 21:53:42'),
(104, 1, 'stores', 'Modificación de registro ID 2 | name: antes ''Almacén Transitorio Equipos II'' - ahora ''Transitorio Equipos II''', '127.0.0.1', '2026-06-20 21:54:34'),
(105, 1, 'Movimientos', 'Compra de inventario (+500) de equipo "Televisor Smart" del Proveedor Huawei hacia el Almacen Transitorio Equipos II', '127.0.0.1', '2026-06-20 21:56:05'),
(106, 1, 'generals', 'Modificación de datos generales: rif, title_report_1', '127.0.0.1', '2026-06-20 22:00:53'),
(107, 1, 'generals', 'Modificación de datos generales: department de ''de la locura'' a ''de la locura extrema'', title_report_1 de ''titulo repo 1 modif'' a ''titulo repo 1''', '127.0.0.1', '2026-06-20 22:05:18'),
(108, 1, 'generals', 'Modificación de datos generales: RIF de ''J-154544543-8'' a ''J-154544543-8xx'', Dpto. de ''de la locura extrema parte dos'' a ''de la locura extrema parte dosxx'', Tít Rep (Stock por Almacén) de ''titulo repo 1a'' a ''titulo repo 1ax'', Subtít Rep (Stock por Alma...', '127.0.0.1', '2026-06-20 23:03:10'),
(109, 1, 'General', 'Modif.: RIF de ''J-154544543-8xx'' a ''J-154544543-8'', Dpto. de ''de la locura extrema parte dosxx'' a ''de la locura extrema parte dos'', Tít Rep (Stock por Almacén) de ''titulo repo 1ax'' a ''titulo repo 1a'', Subtít Rep (Stock por Almacén) de ''subtitulo repo 1...', '127.0.0.1', '2026-06-20 23:05:26'),
(110, 1, 'users', 'Habilitación de usuario: lucas@gmail.com', '127.0.0.1', '2026-06-20 23:07:28'),
(111, 1, 'users', 'Inhabilitación de usuario: lucas@gmail.com', '127.0.0.1', '2026-06-20 23:07:36'),
(112, 1, 'users', 'Modificación de usuario lucas@gmail.com | name: antes ''lucas adams (el de la película de los locos adams) , otro loco mas'' - ahora ''lucas adams''', '127.0.0.1', '2026-06-20 23:07:49'),
(113, 1, 'Usuarios', 'Modificación: admin@gmail.com | name: antes ''Administrador'' - ahora ''Administrador Máximo''', '127.0.0.1', '2026-06-20 23:10:22'),
(114, 1, 'Usuarios', 'Carga de nuevo usuario: padre@gmail.com', '127.0.0.1', '2026-06-20 23:11:23'),
(115, 1, 'Usuarios', 'Cambio de contraseña propio', '127.0.0.1', '2026-06-20 23:12:13'),
(116, 1, 'categories', 'Carga de nuevo registro: {"name":"Televisor"}', '127.0.0.1', '2026-06-20 23:23:47'),
(117, 1, 'categories', 'Modificación de registro ID 8 | name: antes ''Switches'' - ahora ''Switche''', '127.0.0.1', '2026-06-20 23:25:21'),
(118, 1, 'categories', 'Modificación de registro: name: antes ''Cables'' - ahora ''Cabless''', '127.0.0.1', '2026-06-20 23:26:18'),
(119, 1, 'categories', 'Modificación de registro: name: ''Cabless'' - ahora ''Cables''', '127.0.0.1', '2026-06-20 23:27:02'),
(120, 1, 'categories', 'Carga de nuevo registro: {"name":"para borrar"}', '127.0.0.1', '2026-06-20 23:28:13'),
(121, 1, 'categories', 'Eliminación de registro ID 13', '127.0.0.1', '2026-06-20 23:28:22'),
(122, 1, 'categories', 'Carga de nuevo registro: {"name":"rindo"}', '127.0.0.1', '2026-06-20 23:30:48'),
(123, 1, 'categories', 'Eliminación de registro: rindo', '127.0.0.1', '2026-06-20 23:30:54'),
(124, 1, 'Categorías', 'Modificación de Cable: name: ''Cables'' - ahora ''Cable''', '127.0.0.1', '2026-06-20 23:34:25'),
(125, 1, 'Categorías', 'Modificación de name: ''Cable'' - ahora ''Cables''', '127.0.0.1', '2026-06-20 23:35:46'),
(126, 1, 'Categorías', 'Modificación de ''Cables'' - ahora ''Cable''', '127.0.0.1', '2026-06-20 23:37:21'),
(127, 1, 'Categorías', 'Modificación de ''Cable'' - a ''Cables''', '127.0.0.1', '2026-06-20 23:37:50'),
(128, 1, 'Unidades', 'Modificación de ''Caja'' - a ''Cajas''', '127.0.0.1', '2026-06-20 23:37:56'),
(129, 1, 'Marcas/Modelos', 'Modificación de ''P2800'' a ''P2800x''', '127.0.0.1', '2026-06-20 23:38:27'),
(130, 1, 'Marcas/Modelos', 'Modificación de ''Huawei'' a ''Huaweii'', ''P2800x'' a ''P2800xi''', '127.0.0.1', '2026-06-20 23:38:52'),
(131, 1, 'Marcas/Modelos', 'Modificación de ''Huaweii'' a ''Huawei'', ''P2800xi'' a ''P2800''', '127.0.0.1', '2026-06-20 23:39:13'),
(132, 1, 'Categorías', 'Carga de nuevo registro: pulgas', '127.0.0.1', '2026-06-20 23:39:32'),
(133, 1, 'Usuarios', 'Cambio de contraseña propio', '127.0.0.1', '2026-06-21 11:06:59'),
(134, 1, 'Usuarios', 'Inhabilitación de usuario: prueba_user@gmail.com', '127.0.0.1', '2026-07-27 12:55:08'),
(135, 1, 'Usuarios', 'Modificación: lucas@gmail.com | role: antes ''Admin'' - ahora ''Editor''', '127.0.0.1', '2026-07-28 13:15:27'),
(136, 1, 'Usuarios', 'Modificación: prueba_user@gmail.com | role: antes ''Admin'' - ahora ''Visitante''', '127.0.0.1', '2026-07-28 13:15:40'),
(137, 1, 'Usuarios', 'Modificación: pepe@gmail.com | role: antes ''Visitante'' - ahora ''Admin''', '127.0.0.1', '2026-07-28 14:48:26'),
(138, 1, 'Usuarios', 'Modificación: pepe@gmail.com | role: antes ''Admin'' - ahora ''Editor''', '127.0.0.1', '2026-07-28 14:48:39'),
(139, 1, 'Usuarios', 'Modificación: pepe@gmail.com | role: antes ''Editor'' - ahora ''Admin''', '127.0.0.1', '2026-07-28 17:23:45'),
(140, 1, 'Usuarios', 'Modificación: padre@gmail.com | role: antes ''Editor'' - ahora ''4''', '127.0.0.1', '2026-07-28 17:24:16'),
(141, 1, 'Usuarios', 'Carga de nuevo usuario: auditor@gmail.com', '127.0.0.1', '2026-07-28 17:26:39'),
(142, 1, 'Usuarios', 'Inhabilitación de usuario: padre@gmail.com', '127.0.0.1', '2026-07-28 17:26:49'),
(143, 12, 'Usuarios', 'Habilitación de usuario: lucas@gmail.com', '127.0.0.1', '2026-07-28 17:31:40'),
(144, 1, 'Usuarios', 'Carga de nuevo usuario: editor@gmail.com', '127.0.0.1', '2026-07-28 17:34:26'),
(145, 1, 'Usuarios', 'Carga de nuevo usuario: visitante@gmail.com', '127.0.0.1', '2026-07-28 17:35:12'),
(146, 1, 'Usuarios', 'Modificación: editor@gmail.com | name: antes ''editor común'' - ahora ''Editor Común y Corriente''', '127.0.0.1', '2026-07-28 17:35:29'),
(147, 1, 'Movimientos', 'Compra de inventario (+1000) de equipo "Televisor Smart" del Proveedor Huawei hacia el Almacén La Yaguara', '127.0.0.1', '2026-07-28 18:23:26'),
(148, 1, 'General', 'Modif.: RIF de ''J-154544543-8'' a ''J-154544543-9''', '127.0.0.1', '2026-07-28 18:24:07'),
(149, 1, 'Usuarios', 'Carga de nuevo usuario: gerente@gmail.com', '127.0.0.1', '2026-07-28 18:25:02');
INSERT INTO "public"."generals" ("id", "rif", "department", "title_report_1", "subtitle_report_1", "title_report_2", "subtitle_report_2", "title_report_3", "subtitle_report_3", "title_report_4", "subtitle_report_4", "footer", "created_at") VALUES
(1, 'J-154544543-9', 'de la locura extrema parte dos', 'titulo repo 1a', 'subtitulo repo 1a', 'titulo repo 2b', 'subtitulo repo 2b', 'titulo repo 3c', 'subtitulo repo 3c', 'titulo repo 4d', 'subtitulo repo 4d', 'footer nike', '2026-06-13 18:09:55');
INSERT INTO "public"."categories" ("id", "name", "created_at") VALUES
(7, 'Cables', '2026-06-15 18:34:25'),
(8, 'Switche', '2026-06-15 18:34:41'),
(9, 'ODF', '2026-06-15 18:34:47'),
(11, 'Conector', '2026-06-16 17:11:34'),
(12, 'Televisor', '2026-06-20 23:23:47'),
(15, 'pulgas', '2026-06-20 23:39:32');
INSERT INTO "public"."equipment" ("id", "sku", "category_id", "name", "brand_model_id", "unit_id", "umbral", "active", "img_url_one", "img_url_two", "user_id", "created_at") VALUES
(1, '125446', 7, 'Cable monomodo 48 hilos externo', 3, 8, 0, 't', NULL, NULL, 1, '2026-06-15 18:43:25'),
(2, '125445', 7, 'Cable monomodo 24hilos externo', 5, 8, 20000, 't', 'equipments/5kOgRJ6LUh5BbyurEBaHHyV9pIAm9570OH1WDr5J.png', 'equipments/j9UQCjGWhEYbj79jj89HkyhzybqHkpaaTsSlHnsq.png', 1, '2026-06-15 20:09:26'),
(3, '859674', 11, 'Módulo SFP hasta 10KM', 5, 7, 20, 't', 'equipments/TTPgFAvahyugNmSUd34oBzuU4kC9VRzJk64aC4EB.png', 'equipments/sujToAfCp0ouM6KfVLRPQm4ZuiwqekasiFSDVtS5.png', 1, '2026-06-16 17:13:37'),
(4, '809020', 11, 'Modulo SFP hasta 20 km', 3, 7, 20, 't', 'equipments/dDDETO1RJFvrLcylqmn31IlND4FiyLxvnrj61Y6b.png', 'equipments/d3qagd2mL2c2sEWiXjyszfkljmKKiZDy997dUOTv.png', 1, '2026-06-16 21:03:18'),
(5, '809020', 8, 'Televisor Smart', 5, 7, 5, 't', 'equipments/LoNYyWEORNaLXejzSqvJkj7RIIK6SF3txIZ1hYVb.png', 'equipments/gWsLXfkUd1KtdUCRxqpwdnBkcARGQbG4aXm9GAEU.png', 1, '2026-06-16 21:28:01'),
(6, '809021', 9, 'celular XIAOMI redmi 12c', 4, 9, 15, 't', 'equipments/KOcMj7jS5JQCnhdSdG082mTfBH4I4Au2YhKMxS5f.png', 'equipments/O5pMFErSZlLYB7Ffyd50ADBssAqXh0UshWUMKSr3.png', 1, '2026-06-16 21:30:54'),
(7, '80526398', 8, 'Ventilador de Mesa 5 pulgadas', 5, 7, 5, 't', 'equipments/suf9oHEZ9S5Eyu1WGj9L5Ct10L53B967J40DLHmh.png', 'equipments/VorwbnKGBldbVKayazFyYWrKchVvRRelvlpbFc4g.png', 1, '2026-06-20 19:08:09'),
(8, '140000', 11, 'Pendrive de 64GB', 4, 7, 50, 't', 'equipments/LMV4HcIMdZLhAsZQale5sXntd5jHqENIhRxd7GF7.png', 'equipments/b3RvQy8DtaO1RMWc5mg4vKSth85vyHqaNw6rlt9q.png', 1, '2026-06-20 21:52:30');
INSERT INTO "public"."brand_models" ("id", "brand", "model", "created_at") VALUES
(3, 'ZTE', 'Roccan18', '2026-06-15 18:35:47'),
(4, 'ZTE', 'AMR12', '2026-06-15 18:35:54'),
(5, 'Huawei', 'P2800', '2026-06-15 18:36:05');
INSERT INTO "public"."units" ("id", "name", "created_at") VALUES
(7, 'Unidad', '2026-06-15 18:35:16'),
(8, 'Metros', '2026-06-15 18:35:25'),
(9, 'Cajas', '2026-06-15 18:35:35');
INSERT INTO "public"."inventory" ("id", "equipment_id", "store_id", "stock", "last_change", "user_id", "created_at") VALUES
(1, 1, 2, 15000, '2026-06-15 18:53:11', 1, '2026-06-15 18:43:25'),
(2, 2, 2, 12600, '2026-06-15 20:16:01', 1, '2026-06-15 20:09:26'),
(3, 2, 3, 9000, '2026-06-15 20:18:04', 1, '2026-06-15 20:12:55'),
(4, 3, 1, 135, '2026-06-16 17:20:51', 1, '2026-06-16 17:13:37'),
(5, 4, 2, 25, '2026-06-16 21:16:40', 1, '2026-06-16 21:03:18'),
(6, 5, 3, 6, '2026-06-16 21:28:01', 1, '2026-06-16 21:28:01'),
(7, 6, 1, 13, '2026-06-20 21:19:28', 1, '2026-06-16 21:30:54'),
(8, 7, 2, 25, '2026-06-20 21:10:44', 1, '2026-06-20 19:08:09'),
(9, 7, 3, 25, '2026-06-20 21:29:05', 1, '2026-06-20 19:16:33'),
(10, 7, 1, 20, '2026-06-20 21:29:33', 1, '2026-06-20 21:10:44'),
(11, 6, 3, 10, '2026-06-20 21:26:52', 1, '2026-06-20 21:17:26'),
(12, 5, 1, 1200, '2026-07-28 18:23:26', 1, '2026-06-20 21:43:51'),
(13, 5, 2, 550, '2026-06-20 21:56:05', 1, '2026-06-20 21:44:43'),
(14, 8, 2, 100, '2026-06-20 21:53:42', 1, '2026-06-20 21:52:30'),
(15, 8, 3, 56, '2026-06-20 21:53:21', 1, '2026-06-20 21:53:21');
INSERT INTO "public"."stores" ("id", "name", "address", "phone", "contact", "created_at") VALUES
(1, 'La Yaguara', 'Av. Principal de La Yaguara, antes de Makro, Bajada mueblerias, Centro Logistico La Yaguara', '0212-4512356', 'Pedro García', '2026-06-12 20:16:59'),
(2, 'Transitorio Equipos II', 'CNT, Av Libertador', '0212-5001041', 'Rodolfo Pacheco', '2026-06-15 18:37:21'),
(3, 'Caño Amarillo', 'Maracay', '0243-5263636', 'Krisoft Perez', '2026-06-15 18:37:42');
INSERT INTO "public"."movements" ("id", "movement_type", "equipment_id", "supplier_id", "origin_id", "destination_id", "amount", "obs", "user_id", "created_at") VALUES
(1, 1, 1, 1, NULL, 2, 10000, 'compra inicial', 1, '2026-06-15 18:43:25'),
(2, 1, 1, 2, NULL, 2, 5000, NULL, 1, '2026-06-15 18:53:11'),
(3, 1, 2, 2, NULL, 2, 12000, 'compra inicial', 1, '2026-06-15 20:09:26'),
(4, 1, 2, 1, NULL, 2, 4000, 'nueva compra', 1, '2026-06-15 20:11:39'),
(5, 1, 2, 2, NULL, 2, 4000, NULL, 1, '2026-06-15 20:12:15'),
(6, 1, 2, 2, NULL, 3, 1000, NULL, 1, '2026-06-15 20:12:55'),
(7, 1, 2, 2, NULL, 2, 2600, NULL, 1, '2026-06-15 20:14:02'),
(8, 2, 2, NULL, 3, NULL, 500, 'para urb montana', 1, '2026-06-15 20:15:08'),
(9, 3, 2, NULL, 2, 3, 10000, 'nada', 1, '2026-06-15 20:16:01'),
(10, 4, 2, NULL, 3, NULL, 500, 'error en compra anterior', 1, '2026-06-15 20:17:12'),
(11, 4, 2, NULL, 3, NULL, -2000, 'error anterior', 1, '2026-06-15 20:18:04'),
(12, 1, 3, 2, NULL, 1, 35, 'Compra mundial', 1, '2026-06-16 17:13:37'),
(13, 1, 3, 2, NULL, 1, 100, 'prueba', 1, '2026-06-16 17:20:51'),
(14, 1, 4, 1, NULL, 2, 10, 'nada', 1, '2026-06-16 21:03:18'),
(15, 1, 4, 2, NULL, 2, 15, 'cualquiera', 1, '2026-06-16 21:16:40'),
(16, 1, 5, 2, NULL, 3, 6, 'nada', 1, '2026-06-16 21:28:01'),
(17, 1, 6, 1, NULL, 1, 20, 'nada', 1, '2026-06-16 21:30:54'),
(18, 1, 7, 2, NULL, 2, 5, 'ventiladores', 1, '2026-06-20 19:08:09'),
(19, 1, 7, 2, NULL, 3, 7, 'nada', 1, '2026-06-20 19:16:33'),
(20, 1, 7, 2, NULL, 2, 25, NULL, 1, '2026-06-20 19:31:19'),
(21, 2, 6, NULL, 1, NULL, 20, NULL, 1, '2026-06-20 21:02:04'),
(22, 1, 6, 2, NULL, 1, 25, NULL, 1, '2026-06-20 21:02:37'),
(23, 2, 7, NULL, 3, NULL, 7, NULL, 1, '2026-06-20 21:08:51'),
(24, 1, 7, 2, NULL, 3, 36, NULL, 1, '2026-06-20 21:09:22'),
(25, 2, 7, NULL, 3, NULL, 6, NULL, 1, '2026-06-20 21:09:40'),
(26, 3, 7, NULL, 2, 1, 5, NULL, 1, '2026-06-20 21:10:44'),
(27, 3, 6, NULL, 1, 3, 5, NULL, 1, '2026-06-20 21:17:26'),
(28, 3, 6, NULL, 1, 3, 5, NULL, 1, '2026-06-20 21:17:55'),
(29, 3, 6, NULL, 1, 3, 2, NULL, 1, '2026-06-20 21:19:28'),
(30, 4, 6, NULL, 3, NULL, -2, 'Error en carga de compra', 1, '2026-06-20 21:26:52'),
(31, 1, 7, 2, NULL, 1, 45, NULL, 1, '2026-06-20 21:27:56'),
(32, 2, 7, NULL, 1, NULL, 40, 'En casa de Casimiro', 1, '2026-06-20 21:28:37'),
(33, 3, 7, NULL, 3, 1, 5, NULL, 1, '2026-06-20 21:29:05'),
(34, 4, 7, NULL, 1, NULL, 5, 'para que cuadre', 1, '2026-06-20 21:29:33'),
(35, 1, 5, 2, NULL, 1, 400, NULL, 1, '2026-06-20 21:43:51'),
(36, 2, 5, NULL, 1, NULL, 100, 'campo david', 1, '2026-06-20 21:44:16'),
(37, 3, 5, NULL, 1, 2, 50, NULL, 1, '2026-06-20 21:44:43'),
(38, 4, 5, NULL, 1, NULL, -50, 'x error', 1, '2026-06-20 21:45:03'),
(39, 1, 8, 2, NULL, 2, 200, NULL, 1, '2026-06-20 21:52:30'),
(40, 2, 8, NULL, 2, NULL, 34, 'Para instalar en casa de juan', 1, '2026-06-20 21:52:56'),
(41, 3, 8, NULL, 2, 3, 56, 'hacen falta', 1, '2026-06-20 21:53:21'),
(42, 4, 8, NULL, 2, NULL, -10, 'por error', 1, '2026-06-20 21:53:42'),
(43, 1, 5, 2, NULL, 2, 500, NULL, 1, '2026-06-20 21:56:05'),
(44, 1, 5, 2, NULL, 1, 1000, 'prueba', 1, '2026-07-28 18:23:26');
INSERT INTO "public"."suppliers" ("id", "name", "address", "phone", "contact", "rif", "created_at") VALUES
(1, 'ZTE', 'Principal de los Ruices, Municipio Sucre, Estado Miranda, edificio Union, PB', '0212-6898920', 'Luis Mejias Linares', 'J-154544543-2', '2026-06-12 20:15:41'),
(2, 'Huawei', 'Los Ruices, cerca VTV', '04125553232', 'Luis Alfonzo', 'J-154544543-2', '2026-06-15 18:36:49');
INSERT INTO "public"."movement_types" ("id", "name", "created_at", "updated_at") VALUES
(1, 'Compra', NULL, NULL),
(2, 'Salida', NULL, NULL),
(3, 'Traslado', NULL, NULL),
(4, 'Ajuste', NULL, NULL);
INSERT INTO "public"."roles" ("id", "name", "created_at", "updated_at") VALUES
(1, 'Admin', NULL, NULL),
(2, 'Editor', NULL, NULL),
(3, 'Visitante', NULL, NULL),
(4, 'Auditor', NULL, NULL);
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_07_182137_create_log_access_table', 1),
(5, '2026_06_11_230644_create_log_change_table', 2),
(6, '2026_06_12_192343_create_categories_table', 3),
(7, '2026_06_12_192344_create_units_table', 3),
(8, '2026_06_12_192345_create_brand_models_table', 3),
(9, '2026_06_12_192345_create_suppliers_table', 3),
(10, '2026_06_12_192348_create_stores_table', 3),
(11, '2026_06_13_180607_create_generals_table', 4),
(12, '2026_06_15_180631_create_equipment_table', 5),
(13, '2026_06_15_180631_create_inventories_table', 5),
(14, '2026_06_15_180656_create_movements_table', 5),
(17, '2026_07_28_112719_create_movement_types_table', 6),
(18, '2026_07_28_112722_create_roles_table', 6);
INSERT INTO "public"."users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "role", "active", "last_login", "create_for", "created_at", "updated_at") VALUES
(1, 'Administrador Máximo', 'admin@gmail.com', '2026-06-07 17:49:48', '$2y$12$ucIxa1WdOAplY42bLL7hQu8OX9DFPO6l.ZpyKl3kCY1BqXzlVH3wm', NULL, 1, 't', '2026-07-29 16:43:36', NULL, '2026-06-07 17:49:49', '2026-07-29 16:43:36'),
(12, 'Auditor de Sistema', 'auditor@gmail.com', NULL, '$2y$12$QICqoQu/HycYi36SNd1et.FQsFD15bdy0dWpujrGPhywXKWOU5d3W', NULL, 4, 't', '2026-07-28 18:45:14', 1, '2026-07-28 17:26:39', '2026-07-28 18:45:14'),
(13, 'Editor Común y Corriente', 'editor@gmail.com', NULL, '$2y$12$QWbuEkjAWKuhaWcNQiIkYO31O7YHzwLORqHfTGn5zR7QfUcpRQvAO', NULL, 2, 't', '2026-07-28 18:43:26', 1, '2026-07-28 17:34:26', '2026-07-28 18:43:26'),
(14, 'Visitante externo', 'visitante@gmail.com', NULL, '$2y$12$gLU7j90NEHESe5Hy7G3JbOaRwLHy17zFI5GOClDQqV5KE0.euBk/i', NULL, 3, 't', '2026-07-28 18:29:24', 1, '2026-07-28 17:35:12', '2026-07-28 18:29:24'),
(15, 'Gerente de Unidad', 'gerente@gmail.com', NULL, '$2y$12$9DRs2wunhKUxhEOz3Rh4EOrtlNmh.NeF50HT6ktOK.EEPdaHE6t6S', NULL, 1, 't', NULL, 1, '2026-07-28 18:25:02', '2026-07-28 18:25:02');

INSERT INTO "public"."sessions" ("id", "user_id", "ip_address", "user_agent", "payload", "last_activity") VALUES
('FqOxalgHL3rl16HyYYMRgFRnA0cg0escCPWykqUA', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSWNqckgxSnhIZjFHUnU3THVOR0VacTBmb25DSmpRbzBYR1hmWFZ0aiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90YWJsYXMiO3M6NToicm91dGUiO3M6MTI6InRhYmxhcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzg1MzU3ODE2O319', 1785359350);
INSERT INTO "public"."cache" ("key", "value", "expiration") VALUES
('sigpon-cache-lucas@gmail.com|127.0.0.1', 'i:1;', 1785274373),
('sigpon-cache-lucas@gmail.com|127.0.0.1:timer', 'i:1785274373;', 1785274373),
('sigpon-cache-pepe@gmail.com|127.0.0.1', 'i:1;', 1785274410),
('sigpon-cache-pepe@gmail.com|127.0.0.1:timer', 'i:1785274410;', 1785274410);




INSERT INTO "public"."log_access" ("id", "mail", "result", "obs", "created_at") VALUES
(1, 'admin@gmail.com', 't', NULL, '2026-06-07 17:50:22'),
(2, 'editor@gmail.com', 't', NULL, '2026-06-07 17:50:43'),
(3, 'visitante@gmail.com', 't', NULL, '2026-06-07 17:51:08'),
(4, 'admin@gmail.com', 't', NULL, '2026-06-07 17:52:58'),
(5, 'editor@gmail.com', 't', NULL, '2026-06-07 17:58:22'),
(6, 'visitante@gmail.com', 't', NULL, '2026-06-07 17:58:45'),
(7, 'visitante@gmail.com', 't', NULL, '2026-06-07 18:00:52'),
(8, 'editor@gmail.com', 't', NULL, '2026-06-07 18:01:05'),
(9, 'admin@gmail.com', 't', NULL, '2026-06-07 18:01:43'),
(10, 'visitante@gmail.com', 't', NULL, '2026-06-07 18:01:59'),
(11, 'admin@gmail.com', 't', NULL, '2026-06-07 18:02:27'),
(12, 'admin@gmail.com', 't', NULL, '2026-06-07 18:22:32'),
(13, 'admin@gmail.com', 't', NULL, '2026-06-07 18:31:52'),
(14, 'admin@gmail.com', 'f', 'Contraseña errónea', '2026-06-07 18:49:39'),
(15, 'felipe@gmail.com', 'f', 'Correo no existe', '2026-06-07 18:49:48'),
(16, 'admin@gmail.com', 't', NULL, '2026-06-07 18:49:57'),
(17, 'admin@gmail.com', 't', NULL, '2026-06-08 21:37:35'),
(18, 'visitante@gmail.com', 't', NULL, '2026-06-08 22:08:38'),
(19, 'admin@gmail.com', 't', NULL, '2026-06-08 22:08:59'),
(20, 'admin@gmail.com', 't', NULL, '2026-06-08 22:09:20'),
(21, 'visitante@gmail.com', 'f', 'Contraseña errónea', '2026-06-08 22:43:07'),
(22, 'visitante@gmail.com', 't', NULL, '2026-06-08 22:43:14'),
(23, 'admin@gmail.com', 't', NULL, '2026-06-08 22:43:29'),
(24, 'coco@gmail.com', 't', NULL, '2026-06-08 23:03:40'),
(25, 'admin@gmail.com', 't', NULL, '2026-06-08 23:04:29'),
(26, 'visitante@gmail.com', 't', NULL, '2026-06-08 23:04:47'),
(27, 'visitante@gmail.com', 'f', 'Usuario inactivo', '2026-06-08 23:33:26'),
(28, 'admin@gmail.com', 't', NULL, '2026-06-08 23:33:36'),
(29, 'admin@gmail.com', 't', NULL, '2026-06-09 16:16:19'),
(30, 'admin@gmail.com', 't', NULL, '2026-06-09 19:29:55'),
(31, 'admin@gmail.com', 't', NULL, '2026-06-09 19:45:46'),
(32, 'admin@hshs', 'f', 'Correo no existe', '2026-06-09 21:13:57'),
(33, 'admin@gmail.com', 'f', 'Contraseña errónea', '2026-06-09 21:14:07'),
(34, 'admin@gmail.com', 't', NULL, '2026-06-09 21:14:15'),
(35, 'admin@gmail.com', 't', NULL, '2026-06-11 21:33:41'),
(36, 'prueba_user@gmail.com', 't', NULL, '2026-06-11 23:31:56'),
(37, 'admin@gmail.com', 't', NULL, '2026-06-12 19:05:29'),
(38, 'admin@gmail.com', 't', NULL, '2026-06-13 10:13:22'),
(39, 'admin@gmail.com', 't', NULL, '2026-06-13 11:14:40'),
(40, 'admin@gmail.com', 't', NULL, '2026-06-13 11:50:24'),
(41, 'admin@gmail.com', 't', NULL, '2026-06-13 12:05:40'),
(42, 'admin@gmail.com', 't', NULL, '2026-06-13 12:08:10'),
(43, 'admin@gmail.com', 'f', 'Contraseña errónea', '2026-06-13 12:09:58'),
(44, 'admin@gmail.com', 't', NULL, '2026-06-13 12:10:02'),
(45, 'admin@gmail.com', 't', NULL, '2026-06-13 14:27:09'),
(46, 'admin@gmail.com', 't', NULL, '2026-06-13 15:18:02'),
(47, 'admin@gmail.com', 't', NULL, '2026-06-13 15:20:34'),
(48, 'admin@gmail.com', 't', NULL, '2026-06-13 15:23:36'),
(49, 'admin@gmail.com', 't', NULL, '2026-06-13 15:27:06'),
(50, 'admin@gmail.com', 't', NULL, '2026-06-13 15:28:31'),
(51, 'admin@gmail.com', 't', NULL, '2026-06-13 15:42:05'),
(52, 'admin@gmail.com', 'f', 'Contraseña errónea', '2026-06-13 18:56:46'),
(53, 'admin@gmail.com', 't', NULL, '2026-06-13 18:56:52'),
(54, 'admin@gmail.com', 't', NULL, '2026-06-13 19:00:18'),
(55, 'admin@gmail.com', 't', NULL, '2026-06-13 20:04:46'),
(56, 'admin@gmail.com', 't', NULL, '2026-06-13 20:05:17'),
(57, 'addd@kljdkj.com', 'f', 'Correo no existe', '2026-06-13 20:08:52'),
(58, 'admin@gmail.com', 'f', 'Contraseña errónea', '2026-06-13 20:09:01'),
(59, 'admin@gmail.com', 't', NULL, '2026-06-13 20:09:07'),
(60, 'admin@gmail.com', 't', NULL, '2026-06-14 21:20:34'),
(61, 'admin@gmail.com', 't', NULL, '2026-06-15 18:29:31'),
(62, 'admin@gmail.com', 't', NULL, '2026-06-16 16:53:25'),
(63, 'admin@gmail.com', 't', NULL, '2026-06-16 18:01:56'),
(64, 'admin@gmail.com', 't', NULL, '2026-06-20 14:26:11'),
(65, 'admin@gmail.com', 't', NULL, '2026-06-20 19:05:04'),
(66, 'admin@gmail.com', 'f', 'Contraseña errónea', '2026-06-21 11:06:25'),
(67, 'admin@gmail.com', 'f', 'Contraseña errónea', '2026-06-21 11:06:31'),
(68, 'admin@gmail.com', 't', NULL, '2026-06-21 11:06:39'),
(69, 'admin@gmail.com', 't', NULL, '2026-06-27 22:39:29'),
(70, 'admin@gmail.com', 't', NULL, '2026-07-08 18:25:50'),
(71, 'admin@gmail.com', 't', NULL, '2026-07-08 18:27:18'),
(72, 'admin@gmail.com', 't', NULL, '2026-07-09 10:45:42'),
(73, 'admin@gmail.com', 't', NULL, '2026-07-27 11:34:10'),
(74, 'admin@gmail.com', 't', NULL, '2026-07-27 11:36:11'),
(75, 'admin@gmail.com', 't', NULL, '2026-07-27 12:54:23'),
(76, 'admin@gmail.com', 't', NULL, '2026-07-27 20:44:16'),
(77, 'admin@gmail.com', 't', NULL, '2026-07-28 11:16:51'),
(78, 'admin@gmail.com', 't', NULL, '2026-07-28 14:40:40'),
(79, 'auditor@gmail.com', 't', NULL, '2026-07-28 17:27:20'),
(80, 'lucas@gmail.com', 'f', 'Contraseña errónea', '2026-07-28 17:31:53'),
(81, 'admin@gmail.com', 't', NULL, '2026-07-28 17:32:05'),
(82, 'pepe@gmail.com', 'f', 'Contraseña errónea', '2026-07-28 17:32:30'),
(83, 'admin@gmail.com', 't', NULL, '2026-07-28 17:32:39'),
(84, 'visitante@gmail.com', 't', NULL, '2026-07-28 17:35:46'),
(85, 'editor@gmail.com', 't', NULL, '2026-07-28 17:36:19'),
(86, 'admin@gmail.com', 't', NULL, '2026-07-28 17:39:19'),
(87, 'auditor@gmail.com', 't', NULL, '2026-07-28 17:39:44'),
(88, 'editor@gmail.com', 't', NULL, '2026-07-28 17:40:05'),
(89, 'visitante@gmail.com', 't', NULL, '2026-07-28 17:40:22'),
(90, 'admin@gmail.com', 't', NULL, '2026-07-28 17:40:38'),
(91, 'auditor@gmail.com', 't', NULL, '2026-07-28 17:46:26'),
(92, 'editor@gmail.com', 't', NULL, '2026-07-28 17:47:50'),
(93, 'visitante@gmail.com', 't', NULL, '2026-07-28 17:48:30'),
(94, 'admin@gmail.com', 't', NULL, '2026-07-28 17:50:21'),
(95, 'editor@gmail.com', 't', NULL, '2026-07-28 17:50:47'),
(96, 'admin@gmail.com', 't', NULL, '2026-07-28 17:51:12'),
(97, 'editor@gmail.com', 't', NULL, '2026-07-28 17:51:39'),
(98, 'auditor@gmail.com', 't', NULL, '2026-07-28 18:15:04'),
(99, 'admin@gmail.com', 't', NULL, '2026-07-28 18:16:31'),
(100, 'editor@gmail.com', 't', NULL, '2026-07-28 18:17:17'),
(101, 'auditor@gmail.com', 't', NULL, '2026-07-28 18:18:38'),
(102, 'visitante@gmail.com', 't', NULL, '2026-07-28 18:18:56'),
(103, 'admin@gmail.com', 't', NULL, '2026-07-28 18:19:09'),
(104, 'editor@gmail.com', 't', NULL, '2026-07-28 18:21:45'),
(105, 'auditor@gmail.com', 't', NULL, '2026-07-28 18:21:58'),
(106, 'admin@gmail.com', 't', NULL, '2026-07-28 18:22:53'),
(107, 'editor@gmail.com', 't', NULL, '2026-07-28 18:28:10'),
(108, 'visitante@gmail.com', 't', NULL, '2026-07-28 18:29:24'),
(109, 'admin@gmail.com', 't', NULL, '2026-07-28 18:42:11'),
(110, 'editor@gmail.com', 't', NULL, '2026-07-28 18:43:26'),
(111, 'auditor@gmail.com', 't', NULL, '2026-07-28 18:43:48'),
(112, 'auditor@gmail.com', 't', NULL, '2026-07-28 18:45:14'),
(113, 'admin@gmail.com', 't', NULL, '2026-07-28 18:53:32'),
(114, 'admin@gmail.com', 't', NULL, '2026-07-29 16:43:36');
