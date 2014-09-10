using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
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
        private DB db = new DB();
        dynamic result = new JObject();

        // Index page from GET -> Not found
        // GET /User
        [HttpGet]
        public IHttpActionResult Index()
        {
            result.Add("Response", 401);
            result.Add("Message", "Unauthorized access");
            return Ok(result);
        }

        // Registers a user
        // POST /User
        [HttpPost]
        public IHttpActionResult Post(User user)
        {

            if (db.Users.Where(o => o.Username.Equals(user.Username)).Count() == 0) //Checks to see if user already exists
            {
                //Create User
                return Json("CReating user");
            }
            else
                return Json(new Error("UserExists"));


        }

        // Gets a user's information based off ID: Id, Subject, Username, Admin, ApiKey
        // GET /User/ID?apiKey=KEY
        [HttpGet]
        public IHttpActionResult GetUser(int id, string apiKey = "")
        {
            try //Try to pull ApiKey from Database, if not found return Unauthorized
            {
                db.ApiKeys.Where(o => o.Key == apiKey).Single();
            }
            catch
            {
                result.Add("Response", 401);
                result.Add("Message", "Unauthorized access");
                return Ok(result);
            }

            try //Try to pull User from Database, if not found return 404
            {
                var user = db.Users.Where(o => o.Id == id).Single();
         
                result.Add("Response", 200);
                result.User = new JObject();

                result.User.Add("Id", user.Id);

                if (user.subject == 0)
                    result.User.Add("Subject", false);
                else
                    result.User.Add("Subject", true);

                result.User.Add("Username", user.Username);
                result.User.Add("Admin", user.Admin);
                result.User.Add("ApiKey", user.ApiKeys.Single().Key);
                return Ok(result);
            }
            catch
            {
                result.Add("Response", 400);
                result.Add("User", "Not found");

                return Ok(result);
            }

        }
    }
}
