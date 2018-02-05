import random

from faker import Faker
import sqlalchemy

fake = Faker()

metadata = sqlalchemy.MetaData()
table = sqlalchemy.Table('Goods', metadata,
    sqlalchemy.Column('id', sqlalchemy.Integer, primary_key=True),
    sqlalchemy.Column('name', sqlalchemy.String),
    sqlalchemy.Column('description', sqlalchemy.String),
    sqlalchemy.Column('price', sqlalchemy.Integer),
    sqlalchemy.Column('pic_url', sqlalchemy.String),
)

db = sqlalchemy.create_engine('mysql://vk:6IK4l@127.0.0.2:3306/vktask')
conn = db.connect()

NUMBER = 2000

for i in range(NUMBER):
    print(i, end='\r')

    name = fake.word()
    description = fake.text(max_nb_chars=150)
    price = random.randint(250, 10000)
    pic_url = 'https://loremflickr.com/320/240/cat?lock=' + str(random.randint(1, NUMBER*2))

    ins = table.insert().values(name=name, description=description, price=price, pic_url=pic_url)
    conn.execute(ins)
print()

conn.close()
