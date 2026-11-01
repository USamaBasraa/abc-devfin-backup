import axios from 'axios';
export default axios.create(
    {
    baseURL: 'https://172.20.250.153/api',
    headers: {
        'Content-Type': 'application/json'  
    }});
