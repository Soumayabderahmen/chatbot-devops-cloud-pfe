# outputs.tf racine

output "instance_id" {
  value = module.ec2.instance_id
}

output "public_ip" {
  value = module.ec2.public_ip
}

output "ec2_eip" {
  value = module.ec2.ec2_eip
}

# Utilisez le module RDS au lieu des data sources directes
output "rds_endpoint" {
  value = module.rds.rds_endpoint
}

output "rds_port" {
  value = module.rds.rds_port
}

output "rds_database_name" {
  value = module.rds.rds_database_name
}

output "rds_username" {
  value = module.rds.rds_username
}

output "s3_bucket_name" {
  value = module.s3.s3_bucket_name
}