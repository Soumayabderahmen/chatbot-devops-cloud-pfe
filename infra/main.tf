# main.tf racine
terraform {
  required_providers {
    aws = {
      source = "hashicorp/aws"
      # version = "~> 5.0"
    }
    random = {
      source  = "hashicorp/random"
      version = "~> 3.1"
    }
  }
}

provider "aws" {
  region = var.region
}

# Génération d'un suffixe aléatoire pour les noms uniques
resource "random_id" "suffix" {
  byte_length = 4
}

# Module VPC
module "vpc" {
  source              = "./modules/vpc"
  cidr_block          = var.vpc_cidr
  subnet_cidr         = var.subnet_cidr
  private_subnet_cidr = var.private_subnet_cidr
  name                = var.vpc_name
  az                  = var.az
}

# Module EC2
module "ec2" {
  source            = "./modules/ec2"
  ami_id            = "ami-0160e8d70ebc43ee1"
  instance_type     = "t3.2xlarge"
  subnet_id         = module.vpc.public_subnet_id
  security_group_id = module.vpc.security_group_id
  name_prefix       = "chatbot"
  key_name          = var.key_name

}


# Module RDS (pour gérer la RDS existante)
module "rds" {
  source                = "./modules/rds"
  db_identifier         = "database-cocuisinage"
  db_port               = 3306
  ec2_security_group_id = module.vpc.security_group_id
  vpc_id                = module.vpc.vpc_id
}

# Module S3
module "s3" {
  source      = "./modules/s3"
  bucket_name = "braindcode-startup-${random_id.suffix.hex}"
  env         = "dev"
}