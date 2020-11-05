import random
import requests
import datetime

from time import mktime
from faker import Faker


# initialize a faker generator
fake = Faker()

# constants
BUG_STATUS = [
    'new', 'assigned', 'fixing', 
    'pending_review', 'completed', 'reopen'
]
PRIORITY_LEVEL = [1, 2, 3]
TAG_LIST = fake.words(20)


# populate relevant BugAction to prevent discrepancy under statistics with records
def generate_relevant_actions(created_by, bug_action_id, bug_id, bug_status, created_at, developer_user_id, last):
    random_triager = random.randint(24, 28)
    random_reviewer = random.randint(19, 23)

    # to return to main bug fn to populate
    last_person = random_triager
    with open("bug_action.sql", "a") as f:
        f.write("({}, {}, '{}', '', '{}', {}, {})".format(
                bug_action_id, bug_id, 'new',
                'enabled', created_at, created_by 
            ))
        bug_action_id += 1
        while bug_status != 'new':
            f.write(",")
            f.write("({}, {}, '{}', '', '{}', {}, {})".format(
                    bug_action_id, bug_id, 'assigned',
                    'enabled', created_at, random_triager 
                ))
            bug_action_id += 1
            if bug_status == 'assigned':
                break
            f.write(",")
            f.write("({}, {}, '{}', '', '{}', {}, {})".format(
                    bug_action_id, bug_id, 'fixing',
                    'enabled', created_at, random_triager
                ))
            bug_action_id += 1
            if bug_status == 'fixing':
                break
            f.write(",")
            f.write("({}, {}, '{}', '', '{}', {}, {})".format(
                    bug_action_id, bug_id, 'pending_review',
                    'enabled', created_at, developer_user_id
                ))
            bug_action_id += 1
            if bug_status == 'pending_review':
                last_person = developer_user_id
                break
            if bug_status == 'reopen':
                f.write(",")
                f.write("({}, {}, '{}', '', '{}', {}, {})".format(
                        bug_action_id, bug_id, 'reopen',
                        'enabled', created_at,  random_reviewer
                    ))
            else:
                f.write(",")
                f.write("({}, {}, '{}', '', '{}', {}, {})".format(
                        bug_action_id, bug_id, 'completed',
                        'enabled', created_at,  random_reviewer
                    ))
            last_person = random_reviewer
            bug_action_id += 1
            break
        if last:
            f.write(";")
        else:
            f.write(",")

    return last_person, bug_action_id


def generate_random_datetime():
    month = random.randint(1, 11)
    day_dict = {
        1: 31,
        2: 28,
        3: 31,
        4: 30,
        5: 31,
        6: 30,
        7: 31,
        8: 31,
        9: 30,
        10: 31,
        11: 16
    }
    day = random.randint(1, day_dict[month])
    hour = random.randint(0, 23)
    minute = random.randint(0, 59)
    second = random.randint(0, 59)

    # convert into unix timestamp
    return int(mktime(datetime.datetime(2020, month, day, hour, minute, second).timetuple()))


def generate_bug_tags(bug_id, bug_tag_id, last):
    # start of statement
    tag_list = TAG_LIST.copy()
    delete_status = "enabled"
    created_at = generate_random_datetime()
    created_by = random.randint(4,53)

    with open("bug_tag.sql", "a") as f:
        tags_assigned = random.randint(2, 5)
        for i in range(tags_assigned):
            chosen_tag = tag_list.pop(random.randrange(len(tag_list)))
            f.write("({}, {}, '{}', '{}', {}, {})".format(
                    bug_tag_id, bug_id, chosen_tag, 
                    delete_status, created_at, created_by
                ))
            bug_tag_id += 1
            if i != tags_assigned-1:
                f.write(",")
        if last:
            f.write(';')
        else:
            f.write(',')

    return bug_tag_id


def generate_bugs(num_of_bugs):
    # bug properties
    bug_id = 1
    bug_tag_id = 1
    bug_action_id = 2
    last = False

    with open("bug_action.sql", "w") as f:
        f.write("INSERT INTO `bug_action` VALUES ")
    with open("bug_tag.sql", "w") as f:
        f.write("INSERT INTO `bug_tag` VALUES ")
    with open("bug.sql", "w") as f:
        f.write("INSERT INTO `bug` VALUES ")

        for i in range(num_of_bugs):
            if i == num_of_bugs - 1:
                last = True
            title = ' '.join(fake.words(random.randint(2, 5)))
            description = fake.text().replace("'", "") \
                                    .replace("\n", "")

            bug_status = random.choice(BUG_STATUS)

            # bug has been assigned to an existing developer
            if bug_status in ["assigned", "fixing", "pending_review", "completed", "reopen"]:
                developer_user_id = random.randint(4, 18)
            else:
                developer_user_id = "NULL"

            priority_level = random.choice(PRIORITY_LEVEL)
            notes = "NULL"
            delete_status = "enabled"

            created_at = updated_at = generate_random_datetime()
            created_by = random.randint(4, 53)

            updated_by, bug_action_id = generate_relevant_actions(
                        created_by,
                        bug_action_id, bug_id, bug_status,
                        created_at, developer_user_id, last
                    )

            bug_tag_id = generate_bug_tags(bug_id, bug_tag_id, last)

            f.write("({}, '{}', '{}', '{}', {}, {}, {}, '{}', {}, {}, {}, {})".format(
                    bug_id, title, description, 
                    bug_status, priority_level, developer_user_id,
                    notes, delete_status, created_at,
                    created_by, updated_at, updated_by
                ))

            # comma or semicolon depending on whether there are more entries
            if i == num_of_bugs - 1:
                f.write(";")
            else:
                f.write(",")

            print("Bug {} written".format(i+1))
            bug_id += 1


if __name__ == "__main__":
    generate_bugs(500)
