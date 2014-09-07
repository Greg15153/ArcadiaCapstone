using System;
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
        UserContext db = new UserContext();

        // If user goes to a page that isn't supported, shoot not found...
        // GET /User
        public IHttpActionResult Get()
        {
            return Json(new Error("MethodNotFound"));
        }
        public IHttpActionResult Post()
        {
            User greg = new User { Id = 0, Username = "Greg", Password = "Pass", Admin = 0 };
            db.Users.Add(greg);
            db.SaveChanges();

            return Json("Success");
        }
        // Gets a user's information based off ID, if no/incorrect apiKey shoot errors, if not return user data
        // GET /User/id?apiKey=
        public IHttpActionResult GetUser(int id, string apiKey = "")
        {

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
        }


    }
}
