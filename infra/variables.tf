# variables.tf racine 
variable "region" {
  description = "AWS region"
  type        = string
  default     = "eu-west-3"
}

variable "vpc_cidr" {
  description = "CIDR block for VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "subnet_cidr" {
  description = "CIDR block for public subnet"
  type        = string
  default     = "10.0.1.0/24"
}

variable "private_subnet_cidr" {
  description = "CIDR block for private subnet"
  type        = string
  default     = "10.0.2.0/24"
}

variable "vpc_name" {
  description = "Name for the VPC"
  type        = string
  default     = "chatbot-vpc"
}

variable "az" {
  description = "Availability zone for public subnet"
  type        = string
  default     = "eu-west-3a"
}

variable "key_name" {
  description = "Name of the existing SSH key pair in AWS"
  type        = string
}


