---
skills:
  - title: Frontend
    icon: chrome
    items:
      Bootstrap: 3
      HTML: 3
      JavaScript: 3
      jQuery: 3
      Pug: 3
      Vue.js: 3
      CSS: 2
      ESLint: 2
      Jest: 2
      VuePress: 2
      Sass: 1
  - title: Backend
    icon: server
    items: 
      'Node.js': 3
      Express: 3
      MySQL: 3
      PHP: 3
      Redis: 3
      'Python 3': 2
      C: 2
      Jest: 2
      Yarn: 2
      Bash: 1
      MongoDB: 1
  - title: DevOps
    icon: check-square-o
    items:
      Docker: 3
      Git: 3
      Nginx: 3
      Ubuntu: 3
      'GitLab CI': 2
      Apache: 2
      AWS: 2
      GCP: 2
      GitLab: 2
      Ansible: 1
      CentOS: 1
  - title: IoT
    icon: hdd-o
    items:
      'C++': 3
      C: 2
      'LINE Things': 1
      Arduino: 1
---
# <span class="fa fa-bar-chart-o fa-fw"></span> 技能

本頁列出了我學過的程式語言，並大略的分成三個熟悉程度：精通、中等及略懂。

<div class="row">
  <div class="col-md-6 p-md-2">
    <SkillCard :skill="$frontmatter.skills[0]"/>
    <SkillCard :skill="$frontmatter.skills[2]"/>
  </div>
  <div class="col-md-6 p-md-2">
    <SkillCard :skill="$frontmatter.skills[1]"/>
    <SkillCard :skill="$frontmatter.skills[3]"/>
  </div>
</div>
