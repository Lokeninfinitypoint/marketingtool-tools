from fastapi import FastAPI
from appwrite.client import Client
from appwrite.services.users import Users
from appwrite.services.database import Database
from appwrite.services.storage import Storage

# Initialize Appwrite
client = Client()
client \
    .set_endpoint('https://auth.marketingtool.pro/v1') \
    .set_project('marketingtool-pro') \
    .set_key('your-appwrite-key')

# Initialize FastAPI
app = FastAPI()

# Authentication endpoints
@app.post("/signup")
async def signup(user_data: dict):
    users = Users(client)
    result = users.create(
        user_data['email'],
        user_data['password'],
        user_data.get('name', '')
    )
    return {"message": "User created", "user_id": result['$id']}

@app.post("/login")
async def login(credentials: dict):
    users = Users(client)
    session = users.create_session(
        credentials['email'],
        credentials['password']
    )
    return {"session_token": session['$id']}

# Database setup
db = Database(client)

@app.post("/create-report")
async def create_report(report_data: dict):
    result = db.create_document(
        collection_id='reports',
        data=report_data,
        permissions=['*']  # Adjust based on your needs
    )
    return {"report_id": result['$id']}

# Storage setup
storage = Storage(client)

@app.post("/upload-asset")
async def upload_asset(file: UploadFile):
    result = storage.create_file(
        bucket_id='assets',
        file_id='unique()',
        file=file.file
    )
    return {"file_id": result['$id']}
