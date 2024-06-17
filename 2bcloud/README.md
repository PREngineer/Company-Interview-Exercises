# 2bCloud Interview Assessment

Submission for the 2bCloud Interview for the Sr. DevOps Engineer position by Jorge Pabón.

----

# Original Task Details:

Hi,

First, thanks a lot for your time!

As part of the interview phase, the DevOps task is described below.

In order to perform this task, you will receive details of Linux VM (Ubuntu) to which you will connect (SSH) and on which the task is should be performed.

## General Points:

1. The assignment must be submitted no later than XXX at YYY.
2. Feel free to contact me for any question you have.
3. Create a GitHub repository for this task.

## Task Details:

  1. Basic Web Application
  Write a simple and basic "hello world" web site that presents some content (for example: "Hello World") in your preferred language (NodeJS, Python, etc.)
  OR
  Clone an open source project (any project you want, for example: node hello world)

  2. Containerized
  - Write a Dockerfile you will use for building an image of the application
  - Build an image using the Dockerfile you wrote
  - Push the image you built to a registry

    Verification:

  - Run the application
  - Verify the app is running

  3. CI/CD

  - Install Jenkins server on the VM
  - Create a CI/CD pipelines for your application (the application should be deployed on Kubernetes)

  4. Orchestration

  - Install Minikube on the VM
  - By using Jenkins pipelines from previous step, deploy the web application on Kubernetes.
  - The application should be accessible over HTTP.

    Here are the details of the VM:

  - IP Address: XXX.XXX.XXX.XXX
  - Username: XXX
  - Password: XXX

We are available for any question.

Good Luck ! :-)

----

# What was done and why

The following is a description of what was done (for every step in the exercise) and why it was done that way.

## Basic application

I decided to use a simple PHP application. There is no need to compile and build in an interpreted (scripted) language and it allows me to not waste time on this part of the exercise (we are focusing on the DevOps part rather than on Software Engineering for this position). The requirements do not ask for the application to use a compiled language.

## Containerization

I am not putting my Docker Hub credentials in somebody else's machine.  Hence, I am going to build the image locally and push it to a public Docker Hub repository for simplicity's sake. Normally, I would use a tool like Kaniko to build the docker images and push them to the repository as part of a Jenkins pipeline. For deployment to Kubernetes, I would normally use ArgoCD.  You can see examples of that in my GitHub, as well as the actual Jenkins Pipeline links in my virtual CV page (https://jlpc.dns1.us).

  1. Created the Dockerfile to package the application into a container
  2. Changed directory to the folder containing the files
  3. Ran the following command to build the image:

```
docker build -t prengineer/2bcloud:latest .
```

  **Parenthesis ( Verification of the application container )**

  4. Ran the application locally using Docker.

```
docker run -it --rm -p 8080:80 --name 2bcloud prengineer/2bcloud:latest
```

  5. Verified that the application is accessible locally, mapping container port 80 in the container to port 8080 locally.

  **End of Parenthesis**

  6. Pushed the image to Docker Hub

### System Setup

In this step, I will be setting up the necessary requirements for the system to be ready.  I will proceed to configure the whole thing in the next step.

  1. SSH into the machine

```
ssh username@IP
```

  2. Install and validate Java 17 (required by Jenkins)

```
sudo apt-get update -y
sudo apt-get install -y openjdk-17-jre-headless
java -version
```

  3. Install Jenkins (from official document)

```
sudo wget -O /usr/share/keyrings/jenkins-keyring.asc https://pkg.jenkins.io/debian-stable/jenkins.io-2023.key
echo "deb [signed-by=/usr/share/keyrings/jenkins-keyring.asc]" https://pkg.jenkins.io/debian-stable binary/ | sudo tee /etc/apt/sources.list.d/jenkins.list > /dev/null
sudo apt-get update -y
sudo apt-get install -y jenkins
```

  4. Setup Jenkins with defaults using the interface located at http://<VM-IP>:8080 (it can take a while to load)

```
sudo cat /var/lib/jenkins/secrets/initialAdminPassword
```

  5. Install and validate Minikube dependencies (Docker)

```
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release
sudo apt-get update -y
sudo apt-get install -y docker-ce docker-ce-cli containerd.io
sudo usermod -aG docker $USER
docker -v
sudo reboot now
```

  6. Install Minikube & kubectl, validating

```
wget https://storage.googleapis.com/minikube/releases/latest/minikube-linux-amd64
sudo cp minikube-linux-amd64 /usr/local/bin/minikube
sudo chmod +x /usr/local/bin/minikube
curl -LO https://storage.googleapis.com/kubernetes-release/release/`curl -s https://storage.googleapis.com/kubernetes-release/release/stable.txt`/bin/linux/amd64/kubectl
sudo mv kubectl /usr/local/bin/
minikube start --driver=docker
minikube status
kubectl get nodes
```

### CI/CD and Orchestration

This would involve the actual Continuous Integration for this application (testing, packaging, containerizing).  It will actually not be performed in the Pipeline, the container build and push have been done manually for this exercise as I am not putting my personal credentials or connections into a system that doesn't belong to me.  The Pipeline will have blank stages just to represent where these would be.  The pipeline will be in the same repository as a Jenkinsfile.

Given that the Jenkins application is in the same machine that hosts minikube, no credentials for agent connections will be used.  In its place, direct shell calls will be made to deploy the infrastructure.

The Orchestration is performed by minikube.  Given that we only have one machine, I will deploy a single instance of the container application.  In a real world scenario, this would be deployed to different machines, availability zones, or even regions for High Availability.  The Deployment files will be in the same repository.

1. Let Jenkins manage the minikube cluster

Jenkins runs as another user.  It will not have permissions to execute the commands against the cluster.  To fix it I will
  a. Add Jenkins to the docker group
  b. Copy the kube config files for Jenkins to use
  c. Fix the file permissions

```
sudo usermod -aG docker jenkins
sudo mkdir -p /var/lib/jenkins/.kube
```

2. Prepare the Kubeconfig file for Jenkins

```
cat ~/.kube/config
```

Copy the contents into another file

We need to replace the certificate-authority, client-certificate, and client-key with the actual data inside those files.

```
cat /home/user/.minikube/ca.crt | base64 -w 0; echo
cat /home/user/.minikube/profiles/minikube/client.crt | base64 -w 0; echo
cat /home/user/.minikube/profiles/minikube/client.key | base64 -w 0; echo
```

Rename the keys to include '-data' before the colon and replace the value with the full contents of the files.

3. Save the new kubeconfig to a folder that Jenkins's user can actually use

```
sudo nano /var/lib/jenkins/.kube/config
```

Dump the contents of the new config file and save

```
sudo chown jenkins:jenkins /var/lib/jenkins/.kube/config
sudo systemctl restart jenkins
```

4. Run the Pipeline

### Testing

Minikube runs the cluster under a private network.  It never actually assigns an External IP to a service of type LoadBalancer.

In order to access the application, we will need to do it by:

1. Exposing the service using minikube's trickery:

```
minikube service helloworld-service --url -n helloworld
```

It will output the URL to open from the machine running Minikube.

If this machine is an Ubuntu server, you can't view the webpage but you can see its HTML content by using curl:

```
curl http://IP:port
```

If this machine is an Ubuntu Desktop machine, then you can open the URL in the browser and view the application.