# modules/rds/outputs.tf

output "rds_endpoint" {
  description = "RDS instance endpoint"
  value       = data.aws_db_instance.existing.endpoint
}

output "rds_port" {
  description = "RDS instance port"
  value       = data.aws_db_instance.existing.port
}

output "rds_database_name" {
  description = "RDS database name"
  value = "braindcode_startup_studio"
}

output "rds_username" {
  description = "RDS master username"
  value       = data.aws_db_instance.existing.master_username
}

output "rds_security_group_id" {
  description = "RDS security group ID"
  value       = aws_security_group.rds_sg.id
}
