namespace WebAPI.Migrations
{
    using System;
    using System.Data.Entity.Migrations;
    
    public partial class InitialCreate : DbMigration
    {
        public override void Up()
        {
            CreateTable(
                "dbo.ApiKeys",
                c => new
                    {
                        UserId = c.Int(nullable: false),
                        Key = c.String(nullable: false, maxLength: 50),
                    })
                .PrimaryKey(t => new { t.UserId, t.Key })
                .ForeignKey("dbo.Users", t => t.UserId)
                .Index(t => t.UserId);
            
            CreateTable(
                "dbo.Users",
                c => new
                    {
                        Id = c.Int(nullable: false, identity: true),
                        Username = c.String(maxLength: 25, fixedLength: true),
                        Password = c.String(nullable: false),
                        Admin = c.Int(nullable: false),
                    })
                .PrimaryKey(t => t.Id);
            
            CreateTable(
                "dbo.CompletedSurveys",
                c => new
                    {
                        Id = c.Int(nullable: false, identity: true),
                        UserId = c.Int(nullable: false),
                        SurveyId = c.Int(nullable: false),
                        Date = c.DateTime(nullable: false, storeType: "date"),
                    })
                .PrimaryKey(t => t.Id)
                .ForeignKey("dbo.Surveys", t => t.SurveyId)
                .ForeignKey("dbo.Users", t => t.UserId)
                .Index(t => t.UserId)
                .Index(t => t.SurveyId);
            
            CreateTable(
                "dbo.Results",
                c => new
                    {
                        CompletedSurveyId = c.Int(nullable: false),
                        QuestionId = c.Int(nullable: false),
                        Answer = c.String(nullable: false, maxLength: 128),
                    })
                .PrimaryKey(t => new { t.CompletedSurveyId, t.QuestionId, t.Answer })
                .ForeignKey("dbo.Questions", t => t.QuestionId)
                .ForeignKey("dbo.CompletedSurveys", t => t.CompletedSurveyId)
                .Index(t => t.CompletedSurveyId)
                .Index(t => t.QuestionId);
            
            CreateTable(
                "dbo.Questions",
                c => new
                    {
                        Id = c.Int(nullable: false, identity: true),
                        Type = c.String(nullable: false),
                        Question = c.String(nullable: false),
                    })
                .PrimaryKey(t => t.Id);
            
            CreateTable(
                "dbo.SurveyQuestions",
                c => new
                    {
                        SurveyId = c.Int(nullable: false),
                        QuestionId = c.Int(nullable: false),
                        PageNum = c.Int(nullable: false),
                        OrderNum = c.Int(nullable: false),
                    })
                .PrimaryKey(t => new { t.SurveyId, t.QuestionId, t.PageNum, t.OrderNum })
                .ForeignKey("dbo.Surveys", t => t.SurveyId)
                .ForeignKey("dbo.Questions", t => t.QuestionId)
                .Index(t => t.SurveyId)
                .Index(t => t.QuestionId);
            
            CreateTable(
                "dbo.Surveys",
                c => new
                    {
                        Id = c.Int(nullable: false, identity: true),
                        Title = c.String(nullable: false, maxLength: 50),
                        Delay = c.Int(nullable: false),
                    })
                .PrimaryKey(t => t.Id);
            
        }
        
        public override void Down()
        {
            DropForeignKey("dbo.CompletedSurveys", "UserId", "dbo.Users");
            DropForeignKey("dbo.Results", "CompletedSurveyId", "dbo.CompletedSurveys");
            DropForeignKey("dbo.SurveyQuestions", "QuestionId", "dbo.Questions");
            DropForeignKey("dbo.SurveyQuestions", "SurveyId", "dbo.Surveys");
            DropForeignKey("dbo.CompletedSurveys", "SurveyId", "dbo.Surveys");
            DropForeignKey("dbo.Results", "QuestionId", "dbo.Questions");
            DropForeignKey("dbo.ApiKeys", "UserId", "dbo.Users");
            DropIndex("dbo.SurveyQuestions", new[] { "QuestionId" });
            DropIndex("dbo.SurveyQuestions", new[] { "SurveyId" });
            DropIndex("dbo.Results", new[] { "QuestionId" });
            DropIndex("dbo.Results", new[] { "CompletedSurveyId" });
            DropIndex("dbo.CompletedSurveys", new[] { "SurveyId" });
            DropIndex("dbo.CompletedSurveys", new[] { "UserId" });
            DropIndex("dbo.ApiKeys", new[] { "UserId" });
            DropTable("dbo.Surveys");
            DropTable("dbo.SurveyQuestions");
            DropTable("dbo.Questions");
            DropTable("dbo.Results");
            DropTable("dbo.CompletedSurveys");
            DropTable("dbo.Users");
            DropTable("dbo.ApiKeys");
        }
    }
}
