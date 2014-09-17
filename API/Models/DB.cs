namespace API.Models
{
    using System;
    using System.Data.Entity;
    using System.ComponentModel.DataAnnotations.Schema;
    using System.Linq;

    public partial class DB : DbContext
    {
        public DB()
            : base("name=Database")
        {
        }

        public virtual DbSet<ApiKey> ApiKeys { get; set; }
        public virtual DbSet<CompletedSurvey> CompletedSurveys { get; set; }
        public virtual DbSet<Question> Questions { get; set; }
        public virtual DbSet<Result> Results { get; set; }
        public virtual DbSet<SurveyQuestion> SurveyQuestions { get; set; }
        public virtual DbSet<Survey> Surveys { get; set; }
        public virtual DbSet<User> Users { get; set; }

        protected override void OnModelCreating(DbModelBuilder modelBuilder)
        {
            modelBuilder.Entity<CompletedSurvey>()
                .Property(e => e.Date)
                .IsFixedLength();

            modelBuilder.Entity<CompletedSurvey>()
                .HasOptional(e => e.Result)
                .WithRequired(e => e.CompletedSurvey);

            modelBuilder.Entity<Question>()
                .HasMany(e => e.SurveyQuestions)
                .WithRequired(e => e.Question)
                .WillCascadeOnDelete(false);

            modelBuilder.Entity<Survey>()
                .HasMany(e => e.CompletedSurveys)
                .WithRequired(e => e.Survey)
                .WillCascadeOnDelete(false);

            modelBuilder.Entity<Survey>()
                .HasOptional(e => e.SurveyQuestion)
                .WithRequired(e => e.Survey);

            modelBuilder.Entity<User>()
                .HasOptional(e => e.ApiKey)
                .WithRequired(e => e.User);

            modelBuilder.Entity<User>()
                .HasMany(e => e.CompletedSurveys)
                .WithRequired(e => e.User)
                .WillCascadeOnDelete(false);
        }
    }
}