# modules/rds/main.tf

# Data source pour récupérer l'instance RDS existante
data "aws_db_instance" "existing" {
  db_instance_identifier = var.db_identifier
}

#  SG pour la RDS
resource "aws_security_group" "rds_sg" {
  name        = "${var.db_identifier}-sg"
  description = "Allow access to RDS from EC2"
  vpc_id      = var.vpc_id

  tags = {
    Name = "${var.db_identifier}-sg"
  }
}

# Règle autorisant le trafic de l’EC2 vers RDS
resource "aws_security_group_rule" "allow_ec2_to_rds" {
  type                     = "ingress"
  from_port                = var.db_port
  to_port                  = var.db_port
  protocol                 = "tcp"
  source_security_group_id = var.ec2_security_group_id
  security_group_id        = aws_security_group.rds_sg.id
  description              = "Allow EC2 access to RDS"
  }