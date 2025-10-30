import axios from "axios";

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.get("/sanctum/csrf-cookie").catch((error) => console.error(error));
