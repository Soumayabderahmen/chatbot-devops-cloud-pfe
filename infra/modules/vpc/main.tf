# Vérifie s’il existe déjà un VPC avec le nom donné
data "aws_vpcs" "existing" {
  filter {
    name   = "tag:Name"
    values = [var.name]
  }
}

# Crée un VPC uniquement s’il n’existe pas déjà
resource "aws_vpc" "main" {
  count                = length(data.aws_vpcs.existing.ids) == 0 ? 1 : 0
  cidr_block           = var.cidr_block
  enable_dns_support   = true
  enable_dns_hostnames = true

  tags = {
    Name = var.name
  }
}

# Détermine l’ID du VPC (existant ou créé)
locals {
  vpc_id = length(data.aws_vpcs.existing.ids) > 0 ? data.aws_vpcs.existing.ids[0] : aws_vpc.main[0].id
}

# Data source pour récupérer la gateway existante si le VPC existe déjà
data "aws_internet_gateway" "existing" {
  count = length(data.aws_vpcs.existing.ids) > 0 ? 1 : 0
  filter {
    name   = "attachment.vpc-id"
    values = [local.vpc_id]
  }
}

# Internet Gateway (créée seulement si VPC créé)
resource "aws_internet_gateway" "main" {
  count  = length(data.aws_vpcs.existing.ids) == 0 ? 1 : 0
  vpc_id = local.vpc_id

  tags = {
    Name = "${var.name}-igw"
  }
}

# Locale pour sélectionner la bonne IGW (créée ou existante)
locals {
  igw_id = length(aws_internet_gateway.main) > 0 ? aws_internet_gateway.main[0].id : (
    length(data.aws_internet_gateway.existing) > 0 ? data.aws_internet_gateway.existing[0].id : null
  )
}

# Subnet public
resource "aws_subnet" "public" {
  vpc_id                  = local.vpc_id
  cidr_block              = var.subnet_cidr
  map_public_ip_on_launch = true
  availability_zone       = var.az

  tags = {
    Name = "${var.name}-public-subnet"
  }
}

# Subnet privé pour RDS
resource "aws_subnet" "private" {
  vpc_id            = local.vpc_id
  cidr_block        = var.private_subnet_cidr
  availability_zone = var.az

  tags = {
    Name = "${var.name}-private-subnet"
  }
}

# Route table pour subnet public
resource "aws_route_table" "public" {
  vpc_id = local.vpc_id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = local.igw_id
  }

  tags = {
    Name = "${var.name}-public-rt"
  }
}

resource "aws_route_table_association" "public" {
  subnet_id      = aws_subnet.public.id
  route_table_id = aws_route_table.public.id
}

# Route table privée
resource "aws_route_table" "private" {
  vpc_id = local.vpc_id

  tags = {
    Name = "${var.name}-private-rt"
  }
}

resource "aws_route_table_association" "private" {
  subnet_id      = aws_subnet.private.id
  route_table_id = aws_route_table.private.id
}

# Security group
resource "aws_security_group" "allow_http_ssh" {
  name        = "${var.name}-sg"
  description = "Allow HTTP, HTTPS and SSH inbound traffic"
  vpc_id      = local.vpc_id

  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTPS"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    description = "All outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.name}-sg"
  }
}
