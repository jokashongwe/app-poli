import requests
import multiprocessing
import time
import mysql.connector as connector
import logging
from argparse import ArgumentParser
from unidecode import unidecode

from typing import List, Dict, Any, Optional


def get_phones_from_group(group: str) -> List[str]:
    config = {
        "user": "root",
        "password": "roadToInnov24B",
        "host": "127.0.0.1",
        "database": "marketoapp",
        "raise_on_warnings": True,
    }
    group = group.strip()
    group_parts = [part for part in group.split(",") if part]
    cnx = connector.connect(**config)
    cursor = cnx.cursor()
    query = f"SELECT distinct membre.telephone as telephone  FROM membre, tag_membre WHERE tag_membre.membre_id = membre.id AND tag_membre.tag_id IN ({group});"
    if len(group_parts) == 1:
        query = f"SELECT distinct membre.telephone as telephone  FROM membre, tag_membre WHERE tag_membre.membre_id = membre.id AND tag_membre.tag_id = '{group_parts[0]}';"
    # print("Query: ", query)
    cursor.execute(query)
    numbers: List[str] = []

    for telephone in cursor:
        parsed_phone = (
            f"{telephone}".replace("'", "")
            .replace(",", "")
            .replace("(", "")
            .replace(")", "")
        )
        numbers.append(parsed_phone)
        if len(numbers) == 20:
            yield numbers
            numbers = []
    yield numbers

    cursor.close()
    cnx.close()


class BulkSMS:
    def __init__(
        self,
        config: Dict[str, str],
        message: str,
    ) -> None:
        self.config = config
        self.message = message
        self.credentials = None

    def get_routing_group(self, number):
        return "PREMIUM"

    def send_messages(self, destinationList: list[str]=None):
        print("Starting processing")
        parsed_list = [dest.replace("+243","").strip() for dest in destinationList]
        numbers = [f"+243{dest}" for dest in parsed_list] # ajout de 243 au cas ou il n'y en a pas
        url = "https://api.bulksms.com/v1/messages"
        senderName = self.config.get("senderName")
        senderName = senderName if senderName else "repliable"
        newMessage = unidecode(self.message).replace("ndeg", "no.").replace("Ndeg", "No.")
        body = []
        for number in numbers:
            body.append(
                {
                    "from": senderName,
                    "to": [{"type": "INTERNATIONAL", "address": number}],
                    "routingGroup": self.get_routing_group(number),
                    "encoding": "TEXT",
                    "longMessageMaxParts": 99,
                    "body": newMessage,
                    "protocolId": "IMPLICIT",
                    "messageClass": "SIM_SPECIFIC",
                    "deliveryReports": "ALL",
                }
            )
        token = self.config.get("token")
        headers = {
            "Authorization": f"Basic {token}",
            "Content-Type": "application/json",
        }
        response = requests.post(url=url, json=body, headers=headers)
        if response.status_code != 201:
            logging.error(response.text)
            return False
        return True


if __name__ == "__main__":
    parser = ArgumentParser()
    parser.add_argument(
        "-a",
        "--auth",
        dest="auth_header",
        help="The Auth Token for the BULK SMS API",
        metavar="AUTH",
    )
    parser.add_argument(
        "-m", "--message", dest="message", help="The message to send", metavar="MESSAGE"
    )
    parser.add_argument(
        "-s", "--sender", dest="sender", help="Sender name"
    )
    parser.add_argument(
        "-p",
        "--phone",
        dest="phone",
        help="The phone number to send",
        metavar="PHONE",
        required=False,
    )
    parser.add_argument(
        "-g",
        "--group",
        dest="group",
        help="The group of phone numbers to send",
        metavar="GROUPE",
        required=False,
    )

    args = parser.parse_args()

    if args.phone:
        bulk_instance = BulkSMS(
            config={"token": args.auth_header, "senderName": args.sender},
            message=args.message,
        )
        bulk_instance.send_messages(destinationList=[args.phone])

    if args.group:
        for numbers in get_phones_from_group(group=f"{args.group}"):
            bulk_instance = BulkSMS(
                config={"token": args.auth_header, "senderName": args.sender},
                message=args.message,
            )
            bulk_instance.send_messages(destinationList=numbers)
