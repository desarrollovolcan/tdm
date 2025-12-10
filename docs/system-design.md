# Sistema Integral de Gestión para Club de Tenis de Mesa

Este documento especifica un sistema completo para administrar clubes de tenis de mesa usando **PHP 8.2 + Slim (API REST)**, **MySQL 8** y una **UI responsiva en PHP/HTML5 + Bootstrap** con componentes reutilizables. Se cubren modelo de datos, lógica de negocio, endpoints y pantallas clave para satisfacer los requisitos funcionales descritos.

## 1. Arquitectura y seguridad
- **Capas**:
  - API REST en Slim (JWT + refresh tokens, middleware RBAC, validación con Respect/Validation) más workers en CLI para recordatorios, recálculo de rating y cierres de torneo.
  - UI web en PHP server-rendered con fetch API para acciones dinámicas; SPA ligero para chat/calendario con notificaciones web push.
  - MySQL como base de datos principal; Redis opcional para caché de sesiones y notificaciones en tiempo real (WebSocket via Ratchet/Pusher-like).
- **Multi-club**: todos los registros se asocian a `club_id`; el `super_admin` puede crear clubes, gestionar planes de uso del software y ver métricas consolidadas por club.
- **Seguridad**: HTTPS, JWT firmado, hashing de contraseñas con Argon2id, rate limiting, auditoría en tabla `activity_logs`. Cumplimiento GDPR: consentimiento, anonimización lógica, exportación y borrado de datos personales.
- **Backups y resiliencia**: snapshots diarios de MySQL, exportaciones CSV/Excel desde UI para entidades clave, y rotación de logs de aplicación.
- **Observabilidad**: métricas de rendimiento (apdex, latencias) y alertas cuando fallan recordatorios de pago o envío de notificaciones.
- **Integraciones previstas**: conectores opcionales para pasarelas (MercadoPago/Stripe), correo (SMTP/API), WhatsApp Business y S3/MinIO para adjuntos.

## 2. Roles y permisos
Roles mínimos: `super_admin`, `club_admin`, `coach`, `athlete`, `finance`, `guardian`, `guest`.
- Permisos almacenados en `permissions` y asignados vía `role_permissions` y `user_roles` (soporta múltiples roles por usuario y por club).
- Ejemplos de permisos: `club.manage`, `coach.assign`, `training.create`, `attendance.mark`, `performance.record`, `billing.manage`, `chat.use`, `booking.manage`, `report.view`.
- **Matriz resumida**:
  - `super_admin`: CRUD de clubes, planes de software, usuarios plataforma, auditoría global.
  - `club_admin`: CRUD club, sedes, mesas, planes/membresías, usuarios del club, reservas, reportes.
  - `coach`: crear entrenamientos, registrar asistencia y estadísticas, chatear con deportistas/apoderados.
  - `finance`: emitir facturas, registrar pagos, gestionar recordatorios y reportes financieros.
  - `athlete`: reservar mesas/coach, ver panel personal, responder chat, registrar tests auto-reporte si el club lo permite.
  - `guardian`: acceso de solo lectura a asistencia/pagos/rendimiento básico del menor y chat con entrenador.
  - `guest`: pre-registro y solicitud de pruebas; acceso limitado a reservas o pagos de prueba según políticas.
- **Escenarios multi-club**: un mismo usuario puede tener roles distintos por club (ej. coach en Club A y athlete en Club B); el middleware RBAC recibe `club_id` en el JWT y valida pertenencia + rol.
- **Delegación**: el `club_admin` puede delegar permisos específicos (p.ej., `attendance.mark` o `booking.manage`) a asistentes temporales sin otorgar rol completo.

## 3. Modelo de datos (tablas principales)
- **Identidad y pertenencia**
  - `users(id, email, password_hash, first_name, last_name, birthdate, gender, phone, status, created_at)`
  - `clubs(id, name, tax_id, address, city, country, phone, email, logo_url, colors, opening_hours, created_at)`
  - `club_memberships(id, club_id, user_id, primary_role, main_location_id, category, dominant_hand, play_style, grip_type, health_info, state)`
  - `user_roles(id, club_id, user_id, role)`
  - `guardians(id, user_id, athlete_id, relationship, can_pay, can_view_stats)`
  - `activity_logs(id, user_id, club_id, action, entity, entity_id, payload, ip, created_at)`
  - `documents(id, user_id, club_id, type, url, metadata, created_at)` para fichas médicas, consentimientos y certificados.

- **Infraestructura de sedes**
  - `locations(id, club_id, name, address, city, type, contact, status)`
  - `tables(id, location_id, code, surface, status, notes)`
  - `rooms(id, location_id, name, type, capacity, status)`
  - `table_availability(id, table_id, start_at, end_at, is_blocked, reason)`
  - `equipment(id, club_id, name, type, status, notes, assigned_to_location)` para gestión de robopongs, cámaras de videoanálisis, etc.

- **Entrenamientos**
  - `training_plans(id, club_id, title, type, description, focus, intensity_target, created_by, created_at, template_json)`
  - `training_sessions(id, club_id, location_id, table_id, room_id, type, title, description, coach_id, start_at, end_at, capacity, plan_id, status)`
  - `training_participants(id, session_id, athlete_id, assigned_by, is_primary)`
  - `attendance_records(id, session_id, user_id, role, status, reason, marked_by, marked_at)`
  - `training_exercises(id, session_id, category, sub_type, volume_balls, intensity, focus, notes)`
  - `training_goals(id, session_id, athlete_id, title, target_metric, target_value, due_date, status)`
  - `training_feedback(id, session_id, athlete_id, coach_id, text, attachment_url, created_at)`
  - `microcycles(id, club_id, name, start_date, end_date, objective)` y `microcycle_sessions(microcycle_id, session_id, day_index)` para planificación semanal.
  - `attendance_import_jobs(id, session_id, source, status, processed_at, errors_json)` para carga rápida desde CSV u offline.

- **Rendimiento y competencias**
  - `competitions(id, club_id, name, type, format, category, start_date, end_date, location, status)`
  - `matches(id, competition_id, club_id, session_id, date, athlete_id, opponent_id, opponent_type, result, scoreline, venue, notes)`
  - `match_sets(id, match_id, set_number, athlete_points, opponent_points)`
  - `match_stats(id, match_id, serve_points_won_pct, receive_points_won_pct, unforced_errors, forced_errors, winners, fh_drive_eff, fh_loop_eff, fh_block_eff, fh_flick_eff, bh_drive_eff, bh_loop_eff, bh_block_eff, bh_flick_eff, topspin_vs_backspin_eff, topspin_vs_topspin_eff, depth_short_pct, depth_mid_pct, depth_long_pct, target_backhand_pct, target_middle_pct, target_forehand_pct)`
  - `performance_metrics(id, athlete_id, period, rating, internal_rank, win_rate, serve_win_pct, receive_win_pct, unforced_error_trend, notes)`
  - `tests(id, club_id, athlete_id, type, date, score, unit, notes)`
  - `targets(id, athlete_id, title, description, type, baseline_value, target_value, start_date, due_date, status, coach_id)`
  - `target_updates(id, target_id, progress_value, note, updated_by, created_at)`
  - `video_reviews(id, athlete_id, session_id, match_id, coach_id, url, tags, notes, created_at)` para adjuntar análisis de video y feedback.
  - `elo_history(id, athlete_id, value, calculated_at, reason)` para trazabilidad del rating interno.

- **Planes, membresías y finanzas**
  - `plans(id, club_id, name, type, description, sessions_included, period, price, currency, restrictions, tax_rate)`
  - `memberships(id, club_id, athlete_id, plan_id, start_date, end_date, status, auto_renew)`
  - `products(id, club_id, name, kind, price, currency, tax_rate, stock, is_physical)`
  - `invoices(id, club_id, user_id, membership_id, amount, currency, due_date, status, concept, issued_at)`
  - `invoice_items(id, invoice_id, item_type, item_id, description, qty, unit_price, tax_rate)`
  - `payments(id, invoice_id, method, amount, currency, paid_at, reference, status, processor)`
  - `reminders(id, invoice_id, channel, schedule_at, sent_at, status)`
  - `penalties(id, club_id, user_id, booking_id, amount, currency, reason, created_at)` conectada a cancelaciones tardías.
  - `discounts(id, club_id, code, type, value, valid_from, valid_to, applies_to_plan, usage_limit)`.

- **Comunicación y notificaciones**
  - `conversations(id, club_id, title, scope, created_by)`
  - `conversation_participants(id, conversation_id, user_id, role)`
  - `messages(id, conversation_id, sender_id, body, attachments, created_at, relates_to_session_id)`
  - `notifications(id, user_id, type, title, body, payload, channel, sent_at, read_at)`
  - `message_reactions(id, message_id, user_id, emoji, created_at)`
  - `message_reads(id, message_id, user_id, read_at)`
  - `broadcasts(id, club_id, title, body, target_segment, sent_by, sent_at)` para anuncios masivos por categoría o grupo.

- **Reservas**
  - `bookings(id, club_id, user_id, coach_id, table_id, room_id, start_at, end_at, purpose, status, cancellation_policy)`
  - `booking_rules(id, club_id, max_active_per_user, cancel_min_hours, penalties)`
  - `booking_waitlist(id, booking_id, user_id, status)` para listas de espera en horarios populares.

## 4. API REST principal (ejemplos)
- **Auth**: `POST /auth/register`, `POST /auth/login`, `POST /auth/refresh`, `POST /auth/forgot`, `POST /auth/reset`.
- **Club y sedes**: `GET/POST/PUT /clubs`, `GET/POST/PUT/DELETE /clubs/{id}/locations`, `/locations/{id}/tables`, `/availability`.
- **Usuarios y roles**: `GET/POST/PUT /clubs/{id}/members`, `POST /clubs/{id}/members/{userId}/roles`, `DELETE /memberships/{id}`.
- **Entrenamientos**: `POST /trainings`, `GET /trainings?club_id&coach_id&date`, `PUT /trainings/{id}`, `POST /trainings/{id}/participants`, `POST /trainings/{id}/attendance`, `POST /trainings/{id}/exercises`, `POST /trainings/{id}/goals`, `POST /trainings/{id}/feedback`.
- **Rendimiento**: `POST /matches`, `POST /matches/{id}/sets`, `POST /matches/{id}/stats`, `GET /athletes/{id}/performance?from&to`, `POST /tests`, `POST /athletes/{id}/targets`, `POST /targets/{id}/updates`, `POST /videos`, `GET /athletes/{id}/elo-history`.
- **Competencias**: `POST /competitions`, `POST /competitions/{id}/generate-bracket`, `POST /competitions/{id}/matches/{matchId}/result`, `GET /competitions/{id}/table`, `GET /competitions/{id}/schedule`.
- **Planes y cobranzas**: `POST /plans`, `POST /memberships`, `POST /invoices`, `POST /invoices/{id}/payments`, `GET /reports/revenue?from&to`, `GET /reports/debtors`, `POST /reminders/{id}/send`, `POST /payments/webhook` (pasarela externa), `POST /discounts/apply`.
- **Chat y notificaciones**: `POST /conversations`, `POST /conversations/{id}/messages`, `GET /conversations?scope`, `POST /notifications/test`, `PUT /notifications/{id}/read`.
- **Reservas**: `POST /bookings`, `GET /bookings?user_id&date`, `PUT /bookings/{id}/cancel`, `GET /booking-rules/{clubId}`.
- **Ejemplo payload creación de entrenamiento**:
  ```json
  {
    "club_id": 1,
    "type": "group",
    "location_id": 5,
    "table_id": 12,
    "title": "Táctico Sub-13",
    "coach_id": 44,
    "start_at": "2024-08-01T18:00:00Z",
    "end_at": "2024-08-01T19:30:00Z",
    "capacity": 10,
    "participants": [101, 102, 103],
    "plan_id": 3,
    "exercises": [
      {"category": "saque", "sub_type": "corto", "volume_balls": 80, "intensity": 6, "focus": "táctico"},
      {"category": "drive", "sub_type": "fh_loop", "volume_balls": 120, "intensity": 7, "focus": "ofensivo"}
    ]
  }
  ```

## 5. Lógica de negocio clave
- **ACL**: middleware verifica rol y permisos por club. `super_admin` puede listar todos los clubes; otros solo los asociados.
- **Planes y facturación**: al activar `membership`, generar facturación recurrente mensual/trimestral según `period`. Recordatorios automáticos antes del vencimiento y al vencer.
- **Entrenamientos**: validar disponibilidad de mesa/sala; para individuales exigir un solo deportista; limitar capacidad en grupales. Asistencia editable solo por coach o club_admin.
- **Rendimiento**: el registro de partido recalcula rating interno (ELO configurable) y actualiza `performance_metrics`. Se permite rival externo (opponent_type = external).
- **Competencias**: generación de cuadro según formato (liga, eliminación, grupos); almacenamiento de calendario; cierre de ronda automático al ingresar todos los resultados.
- **Chat**: conversaciones 1:1 y grupales; posibilidad de asociar mensaje a `training_session` para feedback puntual. Soporte de adjuntos (S3/local) y notificaciones push.
- **Reservas**: reglas por club; evita solapamientos; penalizaciones por cancelación tardía se reflejan en facturación o en estado del usuario.
- **Objetivos y metas**: los `targets` se vinculan a deportistas; coaches pueden actualizar progreso después de entrenos/tests; se muestran en panel del deportista y del entrenador.
- **Auditoría**: toda operación crítica (`payment.received`, `match.recorded`, `booking.cancelled`) se escribe en `activity_logs` con IP/usuario para trazabilidad.
- **Retención y status**: si un deportista acumula facturas vencidas, se marca `club_memberships.state = suspended` y se bloquean reservas hasta regularizar.
- **Importación**: permitir subir CSV de deportistas/guardians con validación previa; registrar errores en `attendance_import_jobs` o jobs similares.
- **GDPR**: anonimización lógica mediante reemplazo de datos personales, manteniendo registros de pagos y asistencia para auditoría.

## 6. Interfaces de usuario
### 6.1 Panel del Deportista
- **Dashboard**: próximos entrenamientos/partidos, últimos resultados, % partidos ganados, % puntos con saque/recepción, estado de pagos y mensajes recientes.
- **Rendimiento**: gráficos de rating interno, ranking por categoría, tendencias de errores no forzados, efectividad por golpe y zona, histórico de tests físicos/técnicos.
- **Entrenamientos**: agenda, detalle de cada sesión (contenido técnico, notas del entrenador, asistencia, metas próximas), descarga de plan asociado.
- **Competencias**: historial de torneos y partidos, resultados por set, estadísticas vs rivales.
- **Finanzas**: planes contratados, facturas y pagos con descarga de comprobantes PDF, deudas y vencimientos.
- **Comunicación**: chat con entrenadores/apoderados, notificaciones, envío de videos/imágenes.
- **Objetivos**: tablero de metas técnicas/tácticas/físicas con progreso y comentarios del coach.
- **Autogestión**: solicitudes de cambio de horario, descarga de datos personales (GDPR) y botón de anonimización si procede.
- **Indicadores específicos**: tarjeta de carga semanal (volumen de bolas, minutos entrenados), mapa de calor de zonas objetivo (profundidad y ubicación), comparativa saque/recepción por mes.

### 6.2 Panel del Entrenador
- **Dashboard**: grupos asignados, lista de deportistas, próximos entrenamientos y partidos relevantes, alertas de pagos pendientes que afecten asistencia.
- **Planificación**: CRUD de sesiones (grupales o individuales), asignación de mesas/salas, plantillas de ejercicios, carga rápida de asistencia.
- **Seguimiento**: ingreso rápido de estadísticas post-partido, carga de tests, vista 360° del deportista (historial, fortalezas, debilidades, objetivos, volumen/intensidad).
- **Comunicación**: chat 1:1 y grupal por categoría o grupo; feedback vinculado a sesiones; envío de adjuntos.
- **Herramientas móviles**: modo offline para tomar asistencia y sincronizar luego; botones grandes para uso en cancha.
- **Análisis técnico**: checklist de golpes (drive, loop, flick, topspin vs backspin/topspin) con escalas 1–10 y registro de videos etiquetados. Reportes por grupo y por sesión con intensidad promedio.
- **Carga de trabajo**: panel de microciclos con distribución de foco (técnico/táctico/físico/mental) y alertas por exceso de intensidad semanal.

### 6.3 Panel del Administrador / Finanzas
- **KPIs**: deportistas activos, ingresos por mes, tasa media de asistencia, retención, uso de mesas/sedes.
- **Gestión**: clubes, sedes, mesas, entrenadores, deportistas, planes y membresías, políticas de reservas.
- **Reportes**: listado por categoría/plan/estado, ranking interno, productividad de entrenadores (sesiones, feedback), ingresos por plan/entrenador, deudores.
- **Cobranzas**: creación de órdenes, aplicación de pagos, conciliación, recordatorios automáticos, configuración de pasarelas.
- **Cumplimiento**: vista de logs de actividad, trazabilidad de pagos y exportación de datos para auditorías.
- **Finanzas avanzadas**: cuadro de aging de cuentas por cobrar, proyección de ingresos por renovaciones, conciliación de pagos con webhooks y reversos manuales.
- **Operación diaria**: tablero de reservas y ocupación de mesas en tiempo real, con alertas por solapamientos o bloqueos programados.

### 6.4 Módulo de Chat y Notificaciones
- UI tipo bandeja: lista de conversaciones, mensajes en tiempo real, búsqueda por nombre/categoría/grupo, badges de nuevos mensajes, mutear conversación, adjuntos.
- Configuración de notificaciones in-app/email/WhatsApp para eventos: nuevos entrenamientos, cambios/ cancelaciones, mensajes nuevos, recordatorios de asistencia y pago.

### 6.5 Reservas
- Vista de calendario (semana/mes) para deportistas, entrenadores y administrador.
- Filtros por sede, mesa, entrenador y horario; reglas de máximos por usuario; flujos de cancelación con penalización.
- Bloqueo visual de recursos ocupados; integración con `table_availability` para cierres programados.

## 7. Flujos end-to-end
1. **Alta de deportista y plan**: club_admin crea usuario → asigna rol `athlete` y sede/categoría → contrata plan → genera membresía y factura → el finance registra pago o se inicia cobro recurrente.
2. **Asignación y entrenamiento**: admin/coach asigna deportista a grupo → programa entrenamiento grupal en mesa disponible → coach marca asistencia y registra contenido técnico.
3. **Partido y rendimiento**: tras un partido, coach registra sets y estadísticas → se actualiza rating y KPIs → deportista ve gráficas en su panel.
4. **Cobro mensual**: sistema genera factura recurrente → envía recordatorio automático → finance registra pago (o pasarela confirma) → estado del deportista reflejado en panel.
5. **Comunicación**: coach envía feedback vinculado a una sesión → deportista recibe notificación y responde con video → administrador puede auditar historial.
6. **Reserva individual**: deportista consulta calendario → reserva mesa y entrenador individual → reglas validan disponibilidad y máximos → puede cancelar según política.

## 8. Validaciones y manejo de errores
- Validación de dominios (categorías permitidas, estados) mediante ENUMs/constantes.
- Errores en API retornan códigos y payload JSON `{message, code, details}`.
- Logs de actividad y trazabilidad de pagos. Backups de base de datos y exportación CSV/Excel en listados clave (deportistas, pagos, resultados).

## 9. Extensibilidad
- Hooks/eventos internos (`training.session.created`, `payment.received`) para integrar analítica o mensajería externa.
- Interfaces para pasarelas de pago (`PaymentProviderInterface`) y para notificaciones multicanal.
- Posibilidad de exponer GraphQL adicional para apps móviles.

## 10. Mockups/wireframes (texto)
- **Dashboard deportista**: cards horizontales con próximos entrenamientos, KPI de % partidos ganados, gráfico de línea de rating, tabla de facturas con estado.
- **Planificación del coach**: vista calendario semanal, modal de nueva sesión con selector de mesa, chips de deportistas, bloque de ejercicios con intensidad/volumen.
- **Módulo de cobranzas**: tabla de facturas (estado, monto, vencimiento), filtro por plan y estado, botón “Registrar pago”, panel lateral con historial de recordatorios.
- **Chat**: sidebar con conversaciones, panel central de mensajes tipo burbuja, footer con input/adjuntos, badges de notificaciones.

Este diseño cubre los módulos solicitados y está listo para implementarse sobre el stack PHP/MySQL propuesto.
