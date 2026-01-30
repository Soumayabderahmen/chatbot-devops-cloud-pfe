# modules / ec2 : outputs.tf
output "instance_id" {
  description = "ID of the EC2 instance"
  value       = aws_instance.chatbot.id
}

output "public_ip" {
  description = "Public IP of the EC2 instance"
  value       = aws_instance.chatbot.public_ip
}

output "ec2_eip" {
  description = "Elastic IP attached to the EC2 instance"
  value       = aws_eip.ec2_ip.public_ip
}

