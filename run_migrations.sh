#!/bin/bash
set -e
MIGRATION_FILES=(
"database/migrations/create_users_table.php"
"database/migrations/create_ feature_table.php"
"database/migrations/create_failed_jobs_table.php"
"database/migrations/create_personal_access_tokens_table.php"
"database/migrations/create_password_resets_table.php"
"database/migrations/create_role_table.php"
"database/migrations/create_roleAccess_table.php"
"database/migrations/create_employees_table.php"
"database/migrations/create_pic_externals_table.php"
"database/migrations/create_clients_table.php"
"database/migrations/create_placements_table.php"
"database/migrations/create_contract_clients_table.php"
"database/migrations/create_contract_employees_table.php"
"database/migrations/create_employee_documents_table.php"
"database/migrations/create_stamp_infos_table.php"
"database/migrations/create_invoices_table.php"
"database/migrations/create_invoice_items_table.php"
"database/migrations/create_basts_table.php"
"database/migrations/create_document_attachments_table.php"
"database/migrations/add_soft_deletes_to_users_table.php"
"database/migrations/add_logo_to_clients_table.php"
"database/migrations/add_login_tracking_to_users_table.php"
"database/migrations/create_user_sessions_table.php"
)

for MIGRATION_FILE in "${MIGRATION_FILES[@]}"; do
  echo "Running migration: ${MIGRATION_FILE}"
  docker-compose exec app php artisan migrate --path="${MIGRATION_FILE}"
  echo "----------------------------------------"
done

  echo "-------------------Running Seeder---------------------"
  docker-compose exec app php artisan db:seed
  echo "-------------------Finish Running Seeder---------------------"

echo "All migrations have been run individually."
