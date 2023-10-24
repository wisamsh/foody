#! /bin/sh

touch $TFVARSFILE
echo "\
region = \"$AWS_REGION\"
aws_instance_id = \"$AWS_INSTANCE_ID\"
instance_type = \"$INSTANCE_TYPE\"
key_name = \"$KEY_NAME\"
security_group_id = \"$SECURITY_GROUP_ID\"
asg_name = \"$ASG_NAME\"" > $TFVARSFILE 
