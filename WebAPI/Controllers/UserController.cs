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
using WebAPI.Helpers;

namespace WebAPI.Controllers
{
    public class UserController : ApiController
    {
        private DB db = new DB();
        dynamic result = new JObject();
        Helpers.Authorize authorize = new Authorize();

        // Index page from GET -> Not found
        // GET /User
        [HttpGet]
        public IHttpActionResult Index()
        {
            return Json(new Message("Unauthorized"));
        }

        // Registers a user
        // POST /User
        [HttpPost]
        public IHttpActionResult Post(User user, string apiKey = "")
        {
         //   if (!authorize.AdminKey(apiKey))
            //    return Json(new Message("Unauthorized"));
        
                if (db.Users.Where(o => o.Username.Equals(user.Username)).Count() == 0) //Checks to see if user already exists
                {
                    user.Password = authorize.Encrypt(user.Username, user.Password);
                    db.Users.Add(user);
                    
                    return Ok(db.SaveChanges());
                    }
                else
                {
                    result.Add("Response", 200);
                    result.Add("Message", "User already exists");
                    return Ok(result);
                }
        }

        // Gets a user's information based off ID: Id, Subject, Username, Admin, ApiKey
        // GET /User/ID?apiKey=KEY
 
        [HttpGet]
        public IHttpActionResult GetUser(int id, string apiKey = "")
        {

            if (!authorize.UserKey(apiKey))
                return Json(new Message("Unauthorized"));

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
                return Json(new Message("ResourceNotFound"));
            }

        }
    }
}
