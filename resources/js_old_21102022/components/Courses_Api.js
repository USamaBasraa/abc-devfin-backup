import axios from 'axios';
export default axios.create({
    baseURL: 'https://172.20.250.153/api/get_offered_courses',
    headers: {
        'Content-Type': 'application/json'
    }
});
