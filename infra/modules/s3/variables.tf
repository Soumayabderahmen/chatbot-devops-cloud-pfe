# modules/s3/variables.tf

variable "bucket_name" {
  description = "Name of the S3 bucket"
  type        = string
}

variable "env" {
  description = "Environment tag"
  type        = string
}