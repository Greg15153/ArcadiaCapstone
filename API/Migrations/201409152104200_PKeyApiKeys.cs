namespace API.Migrations
{
    using System;
    using System.Data.Entity.Migrations;
    
    public partial class PKeyApiKeys : DbMigration
    {
        public override void Up()
        {
            CreateTable(
                "dbo.ApiKeys",
                c => new
                    {
                        UserId = c.Int(nullable: false),
                        Key = c.String(nullable: false),
                    })
                .PrimaryKey(t => t.UserId)
                .ForeignKey("dbo.Users", t => t.UserId)
                .Index(t => t.UserId);
            
            CreateTable(
                "dbo.Users",
                c => new
                    {
                        Id = c.Int(nullable: false, identity: true),
                        Subject = c.Int(nullable: false),
                        Username = c.String(nullable: false, maxLength: 25),
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
                        Date = c.Binary(nullable: false, fixedLength: true, timestamp: true, storeType: "timestamp"),
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
                        Answer = c.String(nullable: false),
                    })
                .PrimaryKey(t => t.CompletedSurveyId)
                .ForeignKey("dbo.CompletedSurveys", t => t.CompletedSurveyId)
                .Index(t => t.CompletedSurveyId);
            
            CreateTable(
                "dbo.Surveys",
                c => new
                    {
                        Id = c.Int(nullable: false, identity: true),
                        Title = c.String(maxLength: 50),
                        Wait = c.Time(precision: 7),
                    })
                .PrimaryKey(t => t.Id);
            
            CreateTable(
                "dbo.SurveyQuestion",
                c => new
                    {
                        SurveyId = c.Int(nullable: false),
                        QuestionId = c.Int(nullable: false),
                        PageNum = c.Int(nullable: false),
                        OrderNum = c.Int(nullable: false),
                    })
                .PrimaryKey(t => t.SurveyId)
                .ForeignKey("dbo.Questions", t => t.QuestionId)
                .ForeignKey("dbo.Surveys", t => t.SurveyId)
                .Index(t => t.SurveyId)
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
            
        }
        
        public override void Down()
        {
            DropForeignKey("dbo.CompletedSurveys", "UserId", "dbo.Users");
            DropForeignKey("dbo.SurveyQuestion", "SurveyId", "dbo.Surveys");
            DropForeignKey("dbo.SurveyQuestion", "QuestionId", "dbo.Questions");
            DropForeignKey("dbo.CompletedSurveys", "SurveyId", "dbo.Surveys");
            DropForeignKey("dbo.Results", "CompletedSurveyId", "dbo.CompletedSurveys");
            DropForeignKey("dbo.ApiKeys", "UserId", "dbo.Users");
            DropIndex("dbo.SurveyQuestion", new[] { "QuestionId" });
            DropIndex("dbo.SurveyQuestion", new[] { "SurveyId" });
            DropIndex("dbo.Results", new[] { "CompletedSurveyId" });
            DropIndex("dbo.CompletedSurveys", new[] { "SurveyId" });
            DropIndex("dbo.CompletedSurveys", new[] { "UserId" });
            DropIndex("dbo.ApiKeys", new[] { "UserId" });
            DropTable("dbo.Questions");
            DropTable("dbo.SurveyQuestion");
            DropTable("dbo.Surveys");
            DropTable("dbo.Results");
            DropTable("dbo.CompletedSurveys");
            DropTable("dbo.Users");
            DropTable("dbo.ApiKeys");
        }
    }
}
