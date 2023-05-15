# Small script to use emails from emaillist.txt and send email to them.
# Includes an unsubscribe link with a hash using a security code both here locally
# and on the server (in php) (code+email)

# Needs files subject.txt, content.txt (email body), emaillist.txt and emaillist_code.txt

import os.path
import hashlib
from time import sleep

import base64
from email.message import EmailMessage
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError

SCOPES = ['https://www.googleapis.com/auth/gmail.modify']

def main():
	# https://developers.google.com/gmail/api/quickstart/python
	creds = None
	# The file token.json stores the user's access and refresh tokens, and is
	# created automatically when the authorization flow completes for the first
	# time.
	if os.path.exists('token.json'):
		creds = Credentials.from_authorized_user_file('token.json', SCOPES)
	# If there are no (valid) credentials available, let the user log in.
	if not creds or not creds.valid:
		if creds and creds.expired and creds.refresh_token:
			creds.refresh(Request())
		else:
			flow = InstalledAppFlow.from_client_secrets_file(
				'credentials.json', SCOPES)
			creds = flow.run_local_server(port=0)
		# Save the credentials for the next run
		with open('token.json', 'w') as token:
			token.write(creds.to_json())

	subject = '' # just one line, txt
	with open('subject.txt', 'r', encoding="utf-8") as f:
		subject = f.read()

	content = '' # html
	with open('content.txt', 'r', encoding="utf-8") as f:
		content = f.read()

	# get list of emails from this file
	emaillist = []
	with open('emaillist.txt', 'r', encoding="utf-8") as f:
		emaillist = f.readlines()
	emaillist = [x.strip() for x in emaillist]

	# code to add to email address to create a hash to verify the one who is unsubscribing
	# is doing it from his email address (same code is used in php to verify the unsubscribe)
	code = ''
	with open('emaillist_code.txt', 'r', encoding="utf-8") as f:
		code = f.read()

	for email in emaillist:
		fromaddr = "matthias.crazy.email.bot@gmail.com"
		emailHash = hashlib.sha256((code+email).encode()).hexdigest()

		msg = MIMEMultipart()
		msg['From'] = fromaddr
		msg['To'] = email
		msg['Subject'] = subject
		msg.attach(MIMEText(content.format(email=email, emailHash=emailHash), 'html'))
		text = msg.as_string()

		# send mail through the Gmail API
		print('Sending email to: {}'.format(email))
		service = build('gmail', 'v1', credentials=creds)
		
		encoded_message = base64.urlsafe_b64encode(msg.as_bytes()).decode()

		create_message = {
			'raw': encoded_message
		}

		send_message = (
			service.users().messages().send(
				userId="me",
				body=create_message
			).execute()
		)

		# wait a bit so as to not send the emails too rapidly
		sleep(1)

	print('Done sending mail to {} people.'.format(len(emaillist)))

if __name__ == "__main__":
	main()
