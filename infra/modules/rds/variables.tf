# modules/rds/variables.tf

variable "db_identifier" {
  description = "RDS database identifier"
  type        = string
  default     = "database-cocuisinage"
}

variable "db_port" {
  description = "Database port"
  type        = number
  default     = 3306
}

variable "ec2_security_group_id" {
  description = "Security group ID of EC2 instances that need access to RDS"
  type        = string
}

variable "vpc_id" {
  description = "VPC ID for RDS security group"
  type        = string
}