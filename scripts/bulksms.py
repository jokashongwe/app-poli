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
        "password": "",
        "host": "127.0.0.1",
        "database": "bulksmsapp",
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
        country_sender_number: str,
        config: Dict[str, str],
        message: str,
    ) -> None:
        self.country_sender_number = country_sender_number
        self.config = config
        self.message = message
        self.credentials = None

    def send_messages(self, destinationList=None):
        print("Starting processing")
        numbers = destinationList
        url = "https://api.bulksms.com/v1"
        senderName = self.config.get("senderName")
        senderName = senderName if senderName else "repliable"
        body = []
        for number in numbers:
            body.append(
                {
                    "from": senderName,
                    "to": [{"type": "INTERNATIONAL", "address": number}],
                    "routingGroup": "PREMIUM"
                    if "+24399" in number or "+24397" in number
                    else "STANDARD",
                    "encoding": "TEXT",
                    "longMessageMaxParts": 99,
                    "body": unidecode(self.message),
                    "userSuppliedId": f"submission-{int(time.time())}",
                    "protocolId": "IMPLICIT",
                    "messageClass": "SIM_SPECIFIC",
                    "deliveryReports": "ALL",
                }
            )

        token_type = self.credentials.get("token_type")
        access_token = self.credentials.get("access_token")
        headers = {
            "Authorization": f"{token_type} {access_token}",
            "Content-Type": "application/json",
        }
        response = requests.post(url=url, json=body, headers=headers)
        if response.status_code != 201:
            logging.error(response.text)
            return False
        return True
        print("End processing")


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
        bulk_instance = BulkOrange(
            country_sender_number="+2430000",
            config={"auth_header": args.auth_header},
            message=args.message,
        )
        bulk_instance.send_messages(destinationList=[args.phone])

    if args.group:
        for numbers in get_phones_from_group(group=f"{args.group}"):
            bulk_instance = BulkOrange(
                country_sender_number="+2430000",
                config={"auth_header": args.auth_header},
                message=args.message,
            )
            bulk_instance.send_messages(destinationList=numbers)
