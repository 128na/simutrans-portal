
/**
 * import components
 */
import PageLogin from './Components/Pages/PageLogin';
import PageRegister from './Components/Pages/PageRegister';
import PageIndex from './Components/Pages/PageIndex';
import PageCreateArticle from './Components/Pages/PageCreateArticle';
import PageEditArticle from './Components/Pages/PageEditArticle';
import PageEditProfile from './Components/Pages/PageEditProfile';
import PageAnalyticsArticle from './Components/Pages/PageAnalyticsArticle';

const routes = [
    { name: "login", path: '/login', component: PageLogin },
    { name: "register", path: '/register', component: PageRegister },
    { name: "index", path: '/', component: PageIndex },
    { name: "createArticle", path: '/create/:post_type', component: PageCreateArticle },
    { name: "editArticle", path: '/edit/:id', component: PageEditArticle },
    { name: "editProfile", path: '/profile', component: PageEditProfile },
    { name: "analyticsArticle", path: '/analytics', component: PageAnalyticsArticle },
    { path: '*', redirect: { name: 'login' } },
]

export default routes
