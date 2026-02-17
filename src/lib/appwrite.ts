import { Client, Account, Databases, ID } from 'appwrite';

const client = new Client()
  .setEndpoint(import.meta.env.VITE_APPWRITE_ENDPOINT || 'https://api.marketingtool.pro/v1')
  .setProject(import.meta.env.VITE_APPWRITE_PROJECT || '67897cb400019e9d45c1');

const account = new Account(client);
const databases = new Databases(client);

export { client, account, databases, ID };
