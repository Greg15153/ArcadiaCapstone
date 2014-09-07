using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Http;
using System.Net.Http;

namespace WebAPI.Models
{
    public class Error
    {
        public int Response { get; set; }
        public string Message { get; set; }

        public Error()
        {

        }

        public Error(string type){
            switch (type)
            {
                case "UnAuthorized":
                    this.Response = 401;
                    this.Message = "Not Authorized to pull this data";
                    break;
                case "MissingAPI":
                    this.Response = 401;
                    this.Message = "Missing API Key";
                    break;
                case "MethodNotFound":
                    this.Response = 404;
                    this.Message = "Method not found";
                    break;
                case "ResourceNotFound":
                    this.Response = 404;
                    this.Message = "Resource not found";
                    break;
            }
        }

    }
}