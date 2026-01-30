# modules / ec2 : main.tf 
resource "aws_instance" "chatbot" {
  ami                         = var.ami_id
  instance_type               = var.instance_type
  subnet_id                   = var.subnet_id
  vpc_security_group_ids      = [var.security_group_id]
  key_name                    = var.key_name
  associate_public_ip_address = true

  tags = {
    Name = "${var.name_prefix}-ec2"
  }
}

resource "aws_eip" "ec2_ip" {
  instance   = aws_instance.chatbot.id

  tags = {
    Name = "${var.name_prefix}-eip"
  }

  depends_on = [aws_instance.chatbot]
}

