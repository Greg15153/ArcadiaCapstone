using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Web.Http;
using WebAPI.Models;

namespace WebAPI.Controllers
{
    public class UserController : ApiController
    {
        
        // Index page from GET -> Not found
        // GET /User
        [HttpGet]
        public IHttpActionResult Index()
        {
            return Json(new Error("MethodNotFound"));
        }
        
        // Register User ->
        // POST /User
        [HttpPost]
        public IHttpActionResult Post(User user)
        {
            DB db = new DB();

            if (db.Users.Where(o => o.Username.Equals(user.Username)).Count() == 0) //Checks to see if user already exists
            {
                //Create User
                return Json("CReating user");
            }
            else
                return Json(new Error("UserExists"));
    

        }

        // Gets a user's information based off ID, if no/incorrect apiKey shoot errors, if not return user data
        // GET /User/id?apiKey=
        [HttpGet]
        public IHttpActionResult GetUser(int id, string apiKey = "")
        {
            using (DB db = new DB()) {
                try
                {
                    return Json(db.Users.Where(o => o.Id == id).Single());
                }
                catch
                {
                    return Json("Failed");
                }

            }
            /*
            if (apiKey == "")
                return Json(new Error("MissingAPI"));
            else if(apiKey != "12345")
                return Json(new Error("UnAuthorized"));

            User user = new User();
     
            switch (id)
            {
                case 0:
                    user.Id = 0;
                    user.Username = "Greg";
                    user.Password = "Password123";
                    user.Admin = 1;
                    break;
                case 1:
                    user.Id = 1;
                    user.Username = "Mike";
                    user.Password = "Password123";
                    user.Admin = 0;
                    break;
                default:
                    return Json(new Error("ResourceNotFound"));
            }

            return Json(user);
             */
            return Json("testing..");
        }


    }
}
