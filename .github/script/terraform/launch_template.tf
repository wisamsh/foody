resource "aws_launch_template" "main" {
  name = "foody-pipeline-tmpl"
  image_id      = aws_ami_from_instance.main.id
  instance_type = "${var.instance_type}"
  key_name      = "${var.key_name}"
  vpc_security_group_ids = ["${var.security_group_id}"]
}
