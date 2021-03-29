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
from email.message import EmailMessage

from creds import *


# Information for server connection 
#sesServerAddress = 'email-smtp.ca-central-1.amazonaws.com'
sesPort = 465               # 465 is for secure connections like SSL/TLS
# sesUsername = ''
# sesPassword = ''

# Generic email information
emailSentFromAddress = "aoutage@gmail.com"


def sendOutageEmailsToUsers(messages):
    # Create a secure SSL context
    # context = ssl.create_default_context()
    context  = ssl.SSLContext(ssl.PROTOCOL_TLS)


    try:
        # 'with' statement will cause the connection to cease when the block exits.
        with smtplib.SMTP_SSL(sesServerAddress, sesPort, context=context) as server:
            server.login(sesUsername, sesPassword)

            for i in range(len(messages)):
                try:
                    msg = EmailMessage()
                    msg.set_content(messages[i]['emailMessage'])
                    msg['Subject'] = f"OutageAlert Message"
                    msg['From'] = emailSentFromAddress
                    msg['To'] = messages[i]['recipientEmail']

                    server.send_message(msg)
                    # server.sendmail(emailSentFromAddress, messages[i]['recipientEmail'], messages[i]['emailMessage'], )
                    print(f"Sent email to {messages[i]['recipientEmail']}: {messages[i]['emailMessage']}")

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
    i = 0

    while (i < len(OutageUsersByEmail)) and (len(ListOfOutageMessages) > 0) and (len(OutageUsersByEmail) > 0):

        if checkUserHasNoOutageInList(ListOfOutageMessages, OutageUsersByEmail[i]['OutageID']):
            i += 1
            continue

        newMessage  = f"Hello {OutageUsersByEmail[i]['Name']},\r\n"
        newMessage += f"A power outage has been detected. A listing of updates follows:\r\n"
        newMessage += f"---------------------------------------------------------------\r\n"
        newMessage += f"{OutageUsersByEmail[i]['Property Name']}: \r\n"
        newMessage += getMessagesFromList(ListOfOutageMessages, OutageUsersByEmail[i]['OutageID'])
        newMessage += f"\r\n---------------------------------------------------------------\r\n"


        while (i+1 < len(OutageUsersByEmail)) and (OutageUsersByEmail[i]['Name'] == OutageUsersByEmail[i+1]['Name']) and (OutageUsersByEmail[i]['Contact Email'] == OutageUsersByEmail[i+1]['Contact Email']):
                i += 1
                if checkUserHasNoOutageInList(ListOfOutageMessages, OutageUsersByEmail[i]['OutageID']):
                    # i += 1
                    continue
                newMessage += f"{OutageUsersByEmail[i]['Property Name']}: \r\n"
                newMessage += getMessagesFromList(ListOfOutageMessages, OutageUsersByEmail[i]['OutageID'])
                newMessage += f"\r\n---------------------------------------------------------------\r\n"
        
        messages.append(   {'recipientEmail': OutageUsersByEmail[i]['Contact Email'], 
                            'emailMessage': newMessage
                            })
       
        i += 1

    return messages





def createEmailSMSTextMessages(ListOfOutageMessages, OutageUsersByPhone):
    # Reference: ListOfOutageMessages[i](id#,[(),()]) = [ (OutageIDNumber, [ ('key', int_priority, "message"), ('key', int_priority, "message"), ... ]),  ... ]
    # Reference: OutageUsersByPhone[i][key]     where    key = 'OutageID' 'PropertyID', 'Property Name', 'Address', 'Name', 'Phone', 'CarrierEmail'
    # Creates one email-to-text message per property 
    
    carrierEmailDomain = {  'N/A': '',

                            'Telus': '@msg.telus.com',
                            'Koodo': '@msg.telus.com',

                            'Bell Mobility': '@txt.bell.ca',
                            'PC Mobile': '@txt.bell.ca',
                            'Solo Mobile': '@txt.bell.ca',

                            'Rogers': '@pcs.rogers.com',
                            'Chatr': '@pcs.rogers.com',

                            'Freedom Mobile': '@txt.freedommobile.ca',    # "Must send SMS 4000 from phone to activate."??
                            'Fido': '@fido.ca',
                            'Microcell': '',
                            'Virgin Mobile': '@vmobile.ca',
                            'Sasktel': '@sms.sasktel.com'}

    messages = []
    i = 0

    while (i < len(OutageUsersByPhone)) and (len(ListOfOutageMessages) > 0) and (len(OutageUsersByPhone) > 0):

        if checkUserHasNoOutageInList(ListOfOutageMessages, OutageUsersByPhone[i]['OutageID']):
            i += 1
            continue

        newMessage  = f"Hello {OutageUsersByPhone[i]['Name']},\r\n"
        newMessage += f"A power outage has been detected for {OutageUsersByPhone[i]['Property Name']}: \r\n"
        newMessage += getMessagesFromList(ListOfOutageMessages, OutageUsersByPhone[i]['OutageID'])


        
        messages.append(   {'recipientEmail': f"{OutageUsersByPhone[i]['Phone']}{carrierEmailDomain[ OutageUsersByPhone[i]['Provider'] ]}" ,
                            'emailMessage': newMessage
                            })
       
        i += 1

    return messages






def getMessagesFromList(ListOfOutageMessages, OutageID):
    
    outageMessages = ""

    for (OutageIDNumber, msgList) in ListOfOutageMessages:
        if OutageIDNumber == OutageID:
            for (key, priority, msg) in msgList:
                outageMessages += f"  - {msg}\r\n"

    return outageMessages            





def checkUserHasNoOutageInList(ListOfOutageMessages, OutageID):
    
    for (OutageIDNumber, _) in ListOfOutageMessages:
        if OutageIDNumber == OutageID:
            return False

    return True    


{'N/A': '',
'Telus': ,
'Bell Mobility': ,
'Rogers': ,
'Freedom Mobile': ,
'Fido': ,
'Microcell': ,
'PC Mobile': ,
'Solo Mobile': ,
'Virgin Mobile': ,
'Koodo': ,
'Chatr': ,
'Sasktel': }