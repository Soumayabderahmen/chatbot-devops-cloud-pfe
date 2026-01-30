# modules/vpc/variables.tf

variable "cidr_block" {
  description = "CIDR block for VPC"
  type        = string
}

variable "subnet_cidr" {
  description = "CIDR block for public subnet"
  type        = string
}

variable "private_subnet_cidr" {
  description = "CIDR block for private subnet"
  type        = string
}

variable "name" {
  description = "Name prefix for resources"
  type        = string
}

variable "az" {
  description = "Availability zone for public subnet"
  type        = string
}

