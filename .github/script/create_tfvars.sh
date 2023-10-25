#! /bin/sh

touch $TFVARSFILE
echo "\
region = \"$AWS_REGION\"
aws_instance_id = \"$AWS_INSTANCE_ID\"
instance_type = \"$INSTANCE_TYPE\"
key_name = \"$KEY_NAME\"
security_group_id = \"$SECURITY_GROUP_ID\"
asg_name = \"$ASG_NAME\"
asg_desired_capacity = \"$ASG_DESIRED_CAPACITY\"
asg_min_size = \"$ASG_MIN_SIZE\"
asg_max_size = \"$ASG_MAX_SIZE\"" > $TFVARSFILE 
