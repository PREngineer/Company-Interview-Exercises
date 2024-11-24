import boto3
from botocore.exceptions import NoCredentialsError

def lambda_handler(event, context):
  # Prepare the clients
  ec2 = boto3.client('ec2')
  s3 = boto3.client('s3')
  
  #### Section 1 - S3 Buckets ####

  # Create the ten S3 buckets
  for i in range(1, 11):
    bucket_name = f'jorge-mys3bucket{i}'
    s3.create_bucket(Bucket=bucket_name, CreateBucketConfiguration={'LocationConstraint': 'us-east-1'})
  
  #### Section 2 - EC2 Instances ####

  # Get the list of Official x64 ECS Images
  response = ec2.describe_images(
    Filters=[
      {'Name': 'description', 'Values': ['Amazon ECS-Optimized Amazon Linux 2 AMI*']},
      {'Name': 'architecture', 'Values': ['x86_64']}
    ],
    Owners=['amazon'],
    MostRecent=True
  )

  # Store the most recent image
  ami_id = response['Images'][0]['ImageId']
  
  # Launch EC2 instances and create text files for each
  for i in range(1, 11):
    # Define the instance name
    instance_name = f'myinstance{i}'
    # Create it
    ec2.run_instances(
      ImageId=ami_id,
      InstanceType='t3.micro',
      MinCount=1,
      MaxCount=1,
      TagSpecifications=[
        {
          'ResourceType': 'instance',
          'Tags': [
            {'Key': 'Name', 'Value': instance_name}
          ]
        }
      ]
    )
      
    # Create a text file with the instance name
    file_content = f'Instance Name: {instance_name}'
    file_name = f'{instance_name}.txt'
    
    # Upload text file to the corresponding S3 bucket
    bucket_name = f'jorge-mys3bucket{i}'
    try:
      s3.put_object(
        Bucket=bucket_name,
        Key=file_name,
        Body=file_content
      )
    except NoCredentialsError:
      return {
        'statusCode': 403,
        'body': 'Credentials not available.'
      }
    
      
  return {
    'statusCode': 200,
    'body': 'Created ten EC2 instances and ten S3 buckets. Uploaded text files with instance names to respective buckets.'
  }