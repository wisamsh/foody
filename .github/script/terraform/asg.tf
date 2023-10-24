resource "aws_autoscaling_group" "main" {
  name = var.asg_name
  desired_capacity = 5
  min_size = 5
  max_size = 30
  force_delete = true

  launch_template {
    id = aws_launch_template.main.id
    version = aws_launch_template.main.latest_version
  }

  instance_refresh {
    strategy = "Rolling"
    preferences {
      min_healthy_percentage = 50
      skip_matching = false
    }
  }

}
