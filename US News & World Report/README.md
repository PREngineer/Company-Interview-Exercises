# US News & World Report - DevOps Assessment

## Instructions

Upload the code to your personal Github and share the link to the repository with us. We will look at the commits, comments, code syntax, best practices and functionality of the code.

## Tasks

1.	Write a script (in a language of your choice) that creates a “shared” directory on a GNU/Linux server, inside which any developer can read/write, while also allowing any other developer to modify existing files & directories (including those created by other developers). It should accept one or more absolute or relative paths as an argument. Assume developers have ssh/sftp access to the server. Describe any other assumptions about the environment.

2.	Write a script in a supported lambda runtime (https://docs.aws.amazon.com/lambda/latest/dg/lambda-runtimes.html) that will deploy 10 EC2s and 10 s3 buckets with the same configuration.
The EC2s should use the latest official ECS AMI, they should be t3.micro instances and they should be named myinstance1, mysinstance2, myinstance3, and so forth until myinstance10.
The s3 buckets should be called ${yourname}-mys3bucket1, ${yourname}-mys3bucket2 and so forth.
Obtain the IDs of these EC2 instances and record them in separate local files and upload each file to the respective s3 bucket. S3 bucket 1 should have a file listing the ID of instance1, bucket2 should have a file listing the id of instance 2 and so forth.
You can assume that the VPC is already available and you can provide it as a constant variable. Write down any other assumptions as comments in the code.

3.	Write an automated pipeline (preferably a Jenkinspipeline) that builds and deploys the lambda function created above using infrastructure as code (preferably cloudformation). Create a rule to trigger this lambda each Monday at 9am GMT.
Assume that Jenkins is running in AWS and has a role that already has permissions to create any resources.
You can use these instructions to setup Jenkins on AWS if you wish to test 
https://www.jenkins.io/doc/tutorials/tutorial-for-installing-jenkins-on-AWS/

----

You should allocate no more than 4 hours to this. Please deliver even if not complete, answers don’t have to be fully functional, but the general concepts and best practices will need to be present.

Good luck

--

# Jorge's Notes

I don't deploy things to my personal account for interview exercises; hence, I haven't validated tasks 2 and 3.  Task 1 was validated using a WSL Ubuntu environment.

Assessment is organized into 3 files, one for each task.

## Task 1 - (task1.sh)

Based on the description of the task, it doesn't seem to be related to setting up SMB, NFS, FTP or any similar type of file share.  It just sounds like the request is to create a folder in a shared machine and to make sure that all users have rwx permissions.

This script assumes that the user running the script has permissions to create the folder in the path that they provide.

To run it, the user should SSH in to the machine, copy the file, make it executable, and then execute it.  Example:

```
ssh username@IP
<Copy the file or create it by pasting the code>
chmod +x task1.sh
./task1.sh /app/task1
```

## Task 2 - (task2.py)

Based on the description of the task, it seems that I should only concern myself with the code that is put inside the Lambda function.

This is a Python 3 script that has to be pasted into the Lambda function.

I am assuming that the Lambda function has all the proper permissions/roles and configurations.

I have put the minimal amount of information since I wasn't provided much.  Assumed US-East-1 as the region as it tends to be the default and cheaper one.  Didn't specify VPC or any additional details that were not provided.

## Task 3 - (task3-Jenkinsfile)

Based on the description of the task, it seems like I should not concern myself with the actual creation of the Pipeline but just provide the code for it.

Given that the naming of the file is not the standard *Jenkinsfile*, the pipeline has to be made aware of the proper file to use if using a repository.  Otherwise, just put it inline.

For this task I am assuming that the appropriate permissions exist, I have a bucket to store deployment files named *jorge-deploy-bucket*, and all necessary packages and requirements are installed in the Jenkins server.

--

# Closing Remarks

Thanks for your consideration.  I hope that you have a great rest of your day.

- Jorge Pabón Nov/24/2024


--

People Involved
---

Recruiter: Ashley Douglas (adouglas@usnews.com)

Recruiter (Assessment Coordination): Debbie Eschoe (deschoe@usnews.com)

Cloud Director: Pedro Graber

Sr. DevOps Engineer: Chase Anderson

DevOps Engineer: Luis Betancourt