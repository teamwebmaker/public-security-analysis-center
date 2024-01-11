<div class="accordion" id="dashboard">
    <!-- projects -->
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button d-flex gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#projects" aria-expanded="true" aria-controls="projects">
                 <i class="bi bi-folder-plus"></i>
                 <span class="btn-label">პროექტები</span>
            </button>
        </h2>
        <div id="projects" class="accordion-collapse collapse @if($routeName == 'projects.index' || $routeName == 'projects.create' || $routeName == 'projects.edit') show @endif" data-bs-parent="#dashboard">
            <div class="accordion-body">
                <ul class="list-group">
                    <li class="list-group-item  @if($routeName == 'projects.index')  bg-secondary text-white @endif">
                        <a class="nav-link" href="{{ route('projects.index') }}">პროექტების ნახვა</a>
                    </li>
                    <li class="list-group-item  @if($routeName == 'projects.create')  bg-secondary text-white   @endif">
                        <a class="nav-link" href="{{ route('projects.create') }}">პროექტის შექმნა</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- partners -->
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button d-flex gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#partners" aria-expanded="true" aria-controls="partners">
                <i class="bi bi-people"></i>
                <span class="btn-label">პარტნიორები</span>
            </button>
        </h2>
        <div id="partners" class="accordion-collapse collapse @if($routeName == 'partners.index' || $routeName == 'partners.create' || $routeName == 'partners.edit') show @endif" data-bs-parent="#dashboard">
            <div class="accordion-body">
                <ul class="list-group">
                    <li class="list-group-item @if($routeName == 'partners.index')  bg-secondary text-white @endif">
                        <a class="nav-link" href="#">პარტნიორების ნახვა</a>
                    </li>
                    <li class="list-group-item  @if($routeName == 'partners.create')  bg-secondary text-white @endif">
                        <a class="nav-link" href="#">პარტნიორის შექმნა</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- programs -->
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button d-flex gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#programs" aria-expanded="true" aria-controls="programs">
                <i class="bi bi-briefcase"></i>
                <span class="btn-label">სპეც.პროგრამები</span>
            </button>
        </h2>
        <div id="programs" class="accordion-collapse collapse @if($routeName == 'programs.index' || $routeName == 'programs.create' || $routeName == 'programs.edit') show @endif" data-bs-parent="#dashboard">
            <div class="accordion-body">
                <ul class="list-group">
                    <li class="list-group-item @if($routeName == 'programs.index')  bg-secondary text-white @endif">
                        <a class="nav-link" href="#">სპეც.პროგრამების ნახვა</a>
                    </li>
                    <li class="list-group-item  @if($routeName == 'programs.create')  bg-secondary text-white @endif">
                        <a class="nav-link" href="#">სპეც.პროგრამის შექმნა</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- publications -->
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button d-flex gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#publications" aria-expanded="true" aria-controls="publications">
                <i class="bi bi-book"></i>
                <span class="btn-label">პუბლიკაციები</span>
            </button>
        </h2>
        <div id="publications" class="accordion-collapse collapse @if($routeName == 'publications.index' || $routeName == 'publications.create' || $routeName == 'publications.edit')  show @endif" data-bs-parent="#dashboard">
            <div class="accordion-body">
                <ul class="list-group">
                    <li class="list-group-item @if($routeName == 'publications.index')  bg-secondary text-white @endif">
                        <a class="nav-link" href="#">პუბლიკაციების ნახვა</a>
                    </li>
                    <li class="list-group-item @if($routeName == 'publications.create')  bg-secondary text-white   @endif">
                        <a class="nav-link" href="#">პუბლიკაციის შექმნა</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
