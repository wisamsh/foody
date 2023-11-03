resource "aws_ami_from_instance" "main" {
  name   = "foody-pipeline-image"
  source_instance_id = "${var.aws_instance_id}"
}
