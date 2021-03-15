# Outage Alert
# Application Server
# AppModelSESMail
#
# This code will send a list of message objects to the related recipients
#   
# notable reference: https://realpython.com/python-send-email/
#
# ---------------------------------------------------------------------------------------

import smtplib  # SMTP email library
import ssl      # SSL/TLS security


# Information for server connection 
sesServerAddress = 'email-smtp.ca-central-1.amazonaws.com'
sesPort = 465               # 465 is for secure connections like SSL/TLS
sesUsername = ''
sesPassword = ''

# Generic email information
emailSentFromAddress = "infonick@gmail.com"


def sendOutageEmailsToUsers(messages):
    # Create a secure SSL context
    context = ssl.create_default_context()

    try:
        # 'with' statement will cause the connection to cease when the block exits.
        with smtplib.SMTP_SSL(sesServerAddress, sesPort, context=context) as server:
            server.login(sesUsername, sesPassword)

            for i in range(len(messages)):
                try:
                    server.sendmail(emailSentFromAddress, messages[i]['recipientEmail'], messages[i]['emailMessage'])

                except (smtplib.SMTPSenderRefused,
                        smtplib.SMTPRecipientsRefused,
                        smtplib.SMTPDataError,
                        smtplib.SMTPHeloError,
                        smtplib.SMTPNotSupportedError) as e:
                    print(f"An error occured: {e}")
                    print(f"The related email address was: {messages[i]['recipientEmail']}")
                    print(f"The related message was: {messages[i]['emailMessage']}")
                    print(f"\n\n")
                    # TODO: add more specific error handling and logging



    except (smtplib.SMTPException,
            smtplib.SMTPServerDisconnected,
            smtplib.SMTPResponseException,
            smtplib.SMTPSenderRefused,
            smtplib.SMTPRecipientsRefused,
            smtplib.SMTPDataError,
            smtplib.SMTPConnectError,
            smtplib.SMTPHeloError,
            smtplib.SMTPNotSupportedError,
            smtplib.SMTPAuthenticationError) as e:

        print(e)
        # TODO: add more specific error handling and logging




def createEmailMessages(ListOfOutageMessages, OutageUsersByEmail):
    # Reference: ListOfOutageMessages[i](id#,[(),()]) = [ (OutageIDNumber, [ ('key', int_priority, "message"), ('key', int_priority, "message"), ... ]),  ... ]
    # Reference: OutageUsersByEmail[i][key]     where    key = 'OutageID' 'PropertyID', 'Property Name', 'Address', 'Name', 'Contact Email' 
    
    messages = []

    for i in range(len(OutageUsersByEmail)):
        newMessage  = f"Hello {OutageUsersByEmail[i]['Name']},\n"
        newMessage += f"A power outage has been detected. A listing of updates follows:\n"
        newMessage += f"---------------------------------------------------------------\n"
        newMessage += f"{OutageUsersByEmail[i]['Property Name']}: \n"
        newMessages += getMessagesFromList(ListOfOutageMessages, {OutageUsersByEmail[i]['OutageID'])
        newMessage += f"\n---------------------------------------------------------------"

        while (OutageUsersByEmail[i]['Name'] == OutageUsersByEmail[i+1]['Name']) and (OutageUsersByEmail[i]['Contact Email'] == OutageUsersByEmail[i+1]['Contact Email']):
            i += 1
            newMessage += f"{OutageUsersByEmail[i]['Property Name']}: \n"
            newMessages += getMessagesFromList(ListOfOutageMessages, {OutageUsersByEmail[i]['OutageID'])
            newMessage += f"\n---------------------------------------------------------------"
        
        messages.append(   {'recipientEmail': OutageUsersByEmail[i]['Contact Email'], 
                            'emailMessage': newMessage
                            })

    return messages



def getMessagesFromList(ListOfOutageMessages, OutageID):
    
    outageMessages = ""

    for (OutageIDNumber, msgList) in ListOfOutageMessages:
        if OutageIDNumber == OutageID:
            for (key, priority, msg) in msgList:
                outageMessages += f"  - {msg}\n"

    return outageMessages             