import axios from "axios";

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios
  .get("/sanctum/csrf-cookie")
  .then(() => console.log("ok"))
  .catch((error) => console.error(error));
