resource "aws_route53_record" "studio" {
  zone_id = "Z01062013HQ7RTD1O5EMB" # ID de la zone "braindcode.com"
  name    = "studio.braindcode.com" # Sous-domaine 
  type    = "A"
  ttl     = 300


  records = [module.ec2.ec2_eip] # IP publique du module EC2

}
