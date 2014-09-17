using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using API.Models;
using Newtonsoft.Json.Linq;

namespace API.Controllers.Helpers
{
    public class Authorizer
    {
        public dynamic authorize(String level, String apiKey)
        {
            dynamic result = new JObject();
            Boolean check;

            switch (level)
            {
                case "Admin": check = verifyAdmin(apiKey); break;
                case "User": check = verifyApiKey(apiKey); break;
                default : check = verifyAdmin(apiKey);break;
            }

            if (check)
            {
                result.Add("Status", 200);
                result.Add("Message", "Authorized user");
            }
            else
            {
                result.Add("Status", 401);
                result.Add("Message", "Unauthorized user");
            }

            return result;
        }
        //Verifys if ApiKey is in database
        private Boolean verifyApiKey(String apiKey)
        {
            try
            {
                ApiKey info = getApiKeyInfo(apiKey);
                if (info.Key == "null")
                    return false;

                return true;
            }
            catch
            {
                return false;
            }

        }

        private Boolean verifyAdmin(String apiKey)
        {
            ApiKey info = getApiKeyInfo(apiKey);

            if (info.Key == "null")
                return false;

            try
            {
                using (var db = new DB())
                {

                    if (db.Users.Find(info.UserId).Admin > 0)
                        return true;
                    else
                        return false;
                }
            }
            catch
            {
                return false;
            }
        }

        private ApiKey getApiKeyInfo(String apiKey)
        {
            ApiKey keyInfo = new ApiKey();

            try
            {
                using (var db = new DB())
                {
                    keyInfo = db.ApiKeys.Where(o => o.Key == apiKey).Single();
                }
                return keyInfo;
            }
            catch
            {
                keyInfo.UserId = 0;
                keyInfo.Key = "null";
                return keyInfo;
            }
        }
    }
}